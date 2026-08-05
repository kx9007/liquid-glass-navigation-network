<?php
/**
 * 液态玻璃导航 - 后端API
 * 提供管理后台数据持久化与前端数据对接
 */

session_start();
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

require_once __DIR__ . '/config.php';

$DATA_FILE = __DIR__ . '/data.json';

/**
 * 初始化默认数据（基于 config.php 中的配置）
 */
function getDefaults() {
    global $config, $music_playlist, $headline_config, $progress_config;
    return [
        'admin' => [
            'username' => $config['admin_user'],
            'password' => $config['admin_password']
        ],
        'settings' => [
            'siteTitle' => $config['web_title'],
            'siteDesc' => $config['web_profile'],
            'siteFavicon' => $config['web_logo'],
            'showDynamicIsland' => true,
            'showMusicList' => $config['setting_music'],
            'showProgressPanel' => $config['setting_progress'],
            'showNews' => $config['setting_headline'],
            'showSearchTime' => $config['setting_clock'],
            'showDailyQuote' => $config['setting_saying']
        ],
        'notices' => [[
            'id' => 'notice_1',
            'title' => $config['announcement_title'],
            'content' => $config['announcement_content'],
            'url' => $config['announcement_url'],
            'date' => date('Y/m/d')
        ]],
        'musicPlaylist' => array_map(function($m) {
            return [
                'id' => 'music_' . md5($m['music_url']),
                'title' => $m['music_title'],
                'artist' => $m['music_artist'],
                'src' => $m['music_url'],
                'cover' => $m['music_cover']
            ];
        }, $music_playlist),
        'newsItems' => array_map(function($n) {
            return [
                'id' => 'news_' . md5($n['headline_title']),
                'title' => $n['headline_title'],
                'source' => '',
                'image' => '',
                'url' => isset($n['headline_url']) ? $n['headline_url'] : '',
                'content' => isset($n['headline_content']) ? $n['headline_content'] : ''
            ];
        }, $headline_config),
        'progressItems' => array_map(function($p) {
            return [
                'id' => 'prog_' . md5($p['progress_title']),
                'name' => $p['progress_title'],
                'desc' => $p['progress_content'],
                'status' => 'progress',
                'percent' => 50,
                'url' => isset($p['progress_url']) ? $p['progress_url'] : ''
            ];
        }, $progress_config),
        'homeCards' => [],
        'recommendCards' => [],
        'recommendBatches' => [],
        'feedback' => [],
        'visits' => [
            'count' => 0,
            'trend' => []
        ]
    ];
}

/**
 * 读取数据
 */
function readData() {
    global $DATA_FILE;
    if (!file_exists($DATA_FILE)) {
        $defaults = getDefaults();
        writeData($defaults);
        return $defaults;
    }
    $content = file_get_contents($DATA_FILE);
    $data = json_decode($content, true);
    if (!$data) {
        $defaults = getDefaults();
        writeData($defaults);
        return $defaults;
    }
    return $data;
}

/**
 * 写入数据
 */
function writeData($data) {
    global $DATA_FILE;
    file_put_contents($DATA_FILE, json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT), LOCK_EX);
}

/**
 * 发送 JSON 响应
 */
function jsonResponse($data, $code = 200) {
    http_response_code($code);
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

/**
 * 获取 POST 数据
 */
function getPostData() {
    $raw = file_get_contents('php://input');
    $data = json_decode($raw, true);
    if (!$data) {
        $data = $_POST;
    }
    return $data;
}

/**
 * 管理员验证
 */
function requireAuth() {
    if (empty($_SESSION['admin_logged_in'])) {
        jsonResponse(['code' => 401, 'msg' => '未登录或登录已过期'], 401);
    }
}

/**
 * 生成唯一ID
 */
function uid() {
    return date('YmdHis') . base_convert(mt_rand(100000, 999999), 10, 36);
}

/**
 * 获取客户端真实IP（兼容代理）
 */
function getClientIp() {
    $ip = '';
    if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        // X-Forwarded-For 可能是逗号分隔列表，取第一个
        $ips = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
        $ip = trim($ips[0]);
    } elseif (!empty($_SERVER['HTTP_X_REAL_IP'])) {
        $ip = trim($_SERVER['HTTP_X_REAL_IP']);
    } elseif (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = trim($_SERVER['HTTP_CLIENT_IP']);
    } elseif (!empty($_SERVER['REMOTE_ADDR'])) {
        $ip = trim($_SERVER['REMOTE_ADDR']);
    }
    // 验证 IP 格式
    if (!filter_var($ip, FILTER_VALIDATE_IP)) {
        $ip = '0.0.0.0';
    }
    return $ip;
}

/**
 * 反馈 IP 限流检查（基于文件，避免污染主数据）
 * @return array [是否允许, 距离下次可提交的秒数]
 */
function checkFeedbackRateLimit($ip) {
    $limitFile = __DIR__ . '/feedback_rate_limit.json';
    $cooldown = 6 * 3600; // 6 小时
    $now = time();
    $data = [];
    if (file_exists($limitFile)) {
        $raw = file_get_contents($limitFile);
        $data = json_decode($raw, true);
        if (!is_array($data)) $data = [];
    }
    // 清理过期记录（超过 24 小时），避免文件无限膨胀
    foreach ($data as $k => $ts) {
        if ($now - $ts > 24 * 3600) unset($data[$k]);
    }
    // 检查当前 IP
    if (isset($data[$ip])) {
        $elapsed = $now - $data[$ip];
        if ($elapsed < $cooldown) {
            return [false, $cooldown - $elapsed];
        }
    }
    // 记录本次提交时间
    $data[$ip] = $now;
    file_put_contents($limitFile, json_encode($data, JSON_UNESCAPED_UNICODE), LOCK_EX);
    return [true, 0];
}

// ============ 路由 ============
$action = isset($_GET['action']) ? $_GET['action'] : '';
$postData = getPostData();

// ---- 公开接口（无需登录）----
switch ($action) {
    // 获取首页所有数据（前端 index.html 调用）
    case 'public_data':
        $data = readData();
        $result = [
            'settings' => $data['settings'],
            'notices' => $data['notices'],
            'musicPlaylist' => $data['musicPlaylist'],
            'newsItems' => $data['newsItems'],
            'progressItems' => $data['progressItems'],
            'homeCards' => $data['homeCards'] ?? [],
            'recommendCards' => $data['recommendCards'] ?? [],
            'recommendBatches' => $data['recommendBatches'] ?? []
        ];
        jsonResponse(['code' => 0, 'data' => $result]);
        break;

    // 记录访问量
    case 'visit':
        $data = readData();
        $data['visits']['count'] = ($data['visits']['count'] ?? 0) + 1;
        $today = date('Y/m/d');
        $trend = $data['visits']['trend'] ?? [];
        $last = count($trend) > 0 ? $trend[count($trend) - 1] : null;
        if ($last && $last['date'] === $today) {
            $trend[count($trend) - 1]['count']++;
        } else {
            $trend[] = ['date' => $today, 'count' => 1];
        }
        if (count($trend) > 30) {
            $trend = array_slice($trend, -30);
        }
        $data['visits']['trend'] = $trend;
        writeData($data);
        jsonResponse(['code' => 0, 'count' => $data['visits']['count']]);
        break;

    // 提交反馈
    case 'feedback':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            jsonResponse(['code' => 405, 'msg' => '请求方法不允许'], 405);
        }
        $content = isset($postData['content']) ? trim($postData['content']) : '';
        if (empty($content)) {
            jsonResponse(['code' => 400, 'msg' => '内容不能为空'], 400);
        }
        // IP 限流：6 小时内仅允许提交 1 次
        $clientIp = getClientIp();
        list($allowed, $remaining) = checkFeedbackRateLimit($clientIp);
        if (!$allowed) {
            $hours = floor($remaining / 3600);
            $mins = floor(($remaining % 3600) / 60);
            $tip = $hours > 0 ? ($hours . ' 小时 ' . $mins . ' 分钟') : ($mins . ' 分钟');
            jsonResponse(['code' => 429, 'msg' => '提交过于频繁，请 ' . $tip . ' 后再试'], 429);
        }
        $data = readData();
        $data['feedback'][] = [
            'id' => uid(),
            'content' => $content,
            'ip' => $clientIp,
            'time' => date('Y-m-d H:i:s')
        ];
        writeData($data);
        jsonResponse(['code' => 0, 'msg' => '提交成功']);
        break;
}

// ---- 认证接口 ----
switch ($action) {
    case 'login':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            jsonResponse(['code' => 405, 'msg' => '请求方法不允许'], 405);
        }
        $username = isset($postData['username']) ? trim($postData['username']) : '';
        $password = isset($postData['password']) ? $postData['password'] : '';

        if (empty($username) || empty($password)) {
            jsonResponse(['code' => 400, 'msg' => '用户名或密码不能为空'], 400);
        }

        $data = readData();
        if ($username === $data['admin']['username'] && $password === $data['admin']['password']) {
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_user'] = $username;
            jsonResponse(['code' => 0, 'msg' => '登录成功', 'user' => $username]);
        } else {
            jsonResponse(['code' => 401, 'msg' => '用户名或密码错误'], 401);
        }
        break;

    case 'logout':
        $_SESSION = [];
        session_destroy();
        jsonResponse(['code' => 0, 'msg' => '已退出']);
        break;

    case 'check_login':
        if (!empty($_SESSION['admin_logged_in'])) {
            jsonResponse(['code' => 0, 'logged_in' => true, 'user' => $_SESSION['admin_user'] ?? '']);
        } else {
            jsonResponse(['code' => 401, 'logged_in' => false], 401);
        }
        break;
}

// ---- 以下为管理接口（需要登录）----
requireAuth();

$data = readData();

switch ($action) {
    // ============ 仪表盘数据 ============
    case 'dashboard':
        $recTotal = 0;
        // 优先统计 recommendCards（扁平数组），兼容旧 recommendBatches（嵌套数组）
        if (!empty($data['recommendCards'])) {
            $recTotal += count($data['recommendCards']);
        }
        if (!empty($data['recommendBatches'])) {
            foreach ($data['recommendBatches'] as $batch) {
                $recTotal += is_array($batch) ? count($batch) : 0;
            }
        }
        $stats = [
            ['key' => 'visit', 'val' => $data['visits']['count'] ?? 0],
            ['key' => 'notice', 'val' => count($data['notices'] ?? [])],
            ['key' => 'card', 'val' => count($data['homeCards'] ?? [])],
            ['key' => 'recommend', 'val' => $recTotal],
            ['key' => 'feedback', 'val' => count($data['feedback'] ?? [])],
            ['key' => 'music', 'val' => count($data['musicPlaylist'] ?? [])],
            ['key' => 'news', 'val' => count($data['newsItems'] ?? [])],
            ['key' => 'toggle', 'val' => count(array_filter([
                $data['settings']['showMusicList'] ?? false,
                $data['settings']['showProgressPanel'] ?? false,
                $data['settings']['showNews'] ?? false,
                $data['settings']['showSearchTime'] ?? false,
                $data['settings']['showDailyQuote'] ?? false
            ])) . '/5']
        ];
        jsonResponse(['code' => 0, 'stats' => $stats, 'visits' => $data['visits']]);
        break;

    // 获取访问趋势
    case 'visit_trend':
        $days = isset($_GET['days']) ? intval($_GET['days']) : 14;
        $trend = $data['visits']['trend'] ?? [];
        $map = [];
        foreach ($trend as $item) {
            $map[$item['date']] = $item['count'];
        }
        $result = [];
        $today = new DateTime();
        for ($i = $days - 1; $i >= 0; $i--) {
            $d = clone $today;
            $d->modify("-{$i} days");
            $key = $d->format('Y/m/d');
            $result[] = ['date' => $d->format('n/j'), 'count' => $map[$key] ?? 0];
        }
        jsonResponse(['code' => 0, 'data' => $result]);
        break;

    // ============ 站点设置 ============
    case 'get_settings':
        jsonResponse(['code' => 0, 'data' => $data['settings']]);
        break;

    case 'save_settings':
        $fields = ['siteTitle', 'siteDesc', 'siteFavicon'];
        foreach ($fields as $f) {
            if (isset($postData[$f])) {
                $data['settings'][$f] = $postData[$f];
            }
        }
        writeData($data);
        jsonResponse(['code' => 0, 'msg' => '已保存']);
        break;

    // 开关设置
    case 'save_toggles':
        $toggles = ['showDynamicIsland', 'showMusicList', 'showProgressPanel', 'showNews', 'showSearchTime', 'showDailyQuote'];
        foreach ($toggles as $t) {
            if (isset($postData[$t])) {
                $data['settings'][$t] = (bool)$postData[$t];
            }
        }
        writeData($data);
        jsonResponse(['code' => 0, 'msg' => '已保存']);
        break;

    // ============ 管理员账号 ============
    case 'get_account':
        jsonResponse(['code' => 0, 'data' => ['username' => $data['admin']['username']]]);
        break;

    case 'change_password':
        $user = isset($postData['username']) ? trim($postData['username']) : '';
        $old = isset($postData['old_password']) ? $postData['old_password'] : '';
        $nw = isset($postData['new_password']) ? $postData['new_password'] : '';
        $repass = isset($postData['re_password']) ? $postData['re_password'] : '';

        if (empty($user) || empty($old) || empty($nw)) {
            jsonResponse(['code' => 400, 'msg' => '请填写完整信息'], 400);
        }
        if ($old !== $data['admin']['password']) {
            jsonResponse(['code' => 400, 'msg' => '原密码错误'], 400);
        }
        if ($nw !== $repass) {
            jsonResponse(['code' => 400, 'msg' => '两次密码不一致'], 400);
        }
        $data['admin']['username'] = $user;
        $data['admin']['password'] = $nw;
        writeData($data);
        jsonResponse(['code' => 0, 'msg' => '账号已更新']);
        break;

    // ============ 公告管理 ============
    case 'get_notices':
        jsonResponse(['code' => 0, 'data' => $data['notices'] ?? []]);
        break;

    case 'save_notice':
        $title = isset($postData['title']) ? trim($postData['title']) : '';
        $content = isset($postData['content']) ? $postData['content'] : '';
        $url = isset($postData['url']) ? trim($postData['url']) : '';
        if (empty($title)) {
            jsonResponse(['code' => 400, 'msg' => '请输入标题'], 400);
        }
        // 单条模式：只保留一条
        $data['notices'] = [[
            'id' => uid(),
            'title' => $title,
            'content' => $content,
            'url' => $url,
            'date' => date('Y/m/d')
        ]];
        writeData($data);
        jsonResponse(['code' => 0, 'msg' => '公告已发布']);
        break;

    case 'update_notice':
        $id = isset($postData['id']) ? $postData['id'] : '';
        $title = isset($postData['title']) ? trim($postData['title']) : '';
        $content = isset($postData['content']) ? $postData['content'] : '';
        $url = isset($postData['url']) ? trim($postData['url']) : '';
        if (empty($id)) {
            jsonResponse(['code' => 400, 'msg' => '缺少ID'], 400);
        }
        foreach ($data['notices'] as $i => $n) {
            if ($n['id'] === $id) {
                $data['notices'][$i]['title'] = $title;
                $data['notices'][$i]['content'] = $content;
                $data['notices'][$i]['url'] = $url;
                writeData($data);
                jsonResponse(['code' => 0, 'msg' => '已更新']);
            }
        }
        jsonResponse(['code' => 404, 'msg' => '公告不存在'], 404);
        break;

    case 'delete_notice':
        $id = isset($postData['id']) ? $postData['id'] : '';
        $data['notices'] = [];
        writeData($data);
        jsonResponse(['code' => 0, 'msg' => '已删除']);
        break;

    // ============ 音乐管理 ============
    case 'get_music':
        jsonResponse(['code' => 0, 'data' => $data['musicPlaylist'] ?? []]);
        break;

    case 'add_music':
        $item = [
            'id' => uid(),
            'title' => isset($postData['title']) ? trim($postData['title']) : '',
            'artist' => isset($postData['artist']) ? trim($postData['artist']) : '',
            'src' => isset($postData['src']) ? trim($postData['src']) : '',
            'cover' => isset($postData['cover']) ? trim($postData['cover']) : ''
        ];
        if (empty($item['title']) || empty($item['src'])) {
            jsonResponse(['code' => 400, 'msg' => '请填写完整信息'], 400);
        }
        $data['musicPlaylist'][] = $item;
        writeData($data);
        jsonResponse(['code' => 0, 'msg' => '已添加', 'id' => $item['id']]);
        break;

    case 'update_music':
        $id = isset($postData['id']) ? $postData['id'] : '';
        if (empty($id)) {
            jsonResponse(['code' => 400, 'msg' => '缺少ID'], 400);
        }
        foreach ($data['musicPlaylist'] as $i => $m) {
            if ($m['id'] === $id) {
                $data['musicPlaylist'][$i]['title'] = isset($postData['title']) ? trim($postData['title']) : $m['title'];
                $data['musicPlaylist'][$i]['artist'] = isset($postData['artist']) ? trim($postData['artist']) : $m['artist'];
                $data['musicPlaylist'][$i]['src'] = isset($postData['src']) ? trim($postData['src']) : $m['src'];
                $data['musicPlaylist'][$i]['cover'] = isset($postData['cover']) ? trim($postData['cover']) : $m['cover'];
                writeData($data);
                jsonResponse(['code' => 0, 'msg' => '已更新']);
            }
        }
        jsonResponse(['code' => 404, 'msg' => '音乐不存在'], 404);
        break;

    case 'delete_music':
        $id = isset($postData['id']) ? $postData['id'] : '';
        $data['musicPlaylist'] = array_values(array_filter($data['musicPlaylist'] ?? [], function($m) use ($id) {
            return $m['id'] !== $id;
        }));
        writeData($data);
        jsonResponse(['code' => 0, 'msg' => '已删除']);
        break;

    // ============ 新闻头条管理 ============
    case 'get_news':
        jsonResponse(['code' => 0, 'data' => $data['newsItems'] ?? []]);
        break;

    case 'add_news':
        $item = [
            'id' => uid(),
            'title' => isset($postData['title']) ? trim($postData['title']) : '',
            'source' => isset($postData['source']) ? trim($postData['source']) : '',
            'image' => isset($postData['image']) ? trim($postData['image']) : '',
            'url' => isset($postData['url']) ? trim($postData['url']) : ''
        ];
        if (empty($item['title'])) {
            jsonResponse(['code' => 400, 'msg' => '请输入标题'], 400);
        }
        $data['newsItems'][] = $item;
        writeData($data);
        jsonResponse(['code' => 0, 'msg' => '已添加', 'id' => $item['id']]);
        break;

    case 'update_news':
        $id = isset($postData['id']) ? $postData['id'] : '';
        if (empty($id)) {
            jsonResponse(['code' => 400, 'msg' => '缺少ID'], 400);
        }
        foreach ($data['newsItems'] as $i => $n) {
            if ($n['id'] === $id) {
                $data['newsItems'][$i]['title'] = isset($postData['title']) ? trim($postData['title']) : $n['title'];
                $data['newsItems'][$i]['source'] = isset($postData['source']) ? trim($postData['source']) : $n['source'];
                $data['newsItems'][$i]['image'] = isset($postData['image']) ? trim($postData['image']) : $n['image'];
                $data['newsItems'][$i]['url'] = isset($postData['url']) ? trim($postData['url']) : $n['url'];
                writeData($data);
                jsonResponse(['code' => 0, 'msg' => '已更新']);
            }
        }
        jsonResponse(['code' => 404, 'msg' => '新闻不存在'], 404);
        break;

    case 'delete_news':
        $id = isset($postData['id']) ? $postData['id'] : '';
        $data['newsItems'] = array_values(array_filter($data['newsItems'] ?? [], function($n) use ($id) {
            return $n['id'] !== $id;
        }));
        writeData($data);
        jsonResponse(['code' => 0, 'msg' => '已删除']);
        break;

    // ============ 进度管理 ============
    case 'get_progress':
        jsonResponse(['code' => 0, 'data' => $data['progressItems'] ?? []]);
        break;

    case 'add_progress':
        $item = [
            'id' => uid(),
            'name' => isset($postData['name']) ? trim($postData['name']) : '',
            'desc' => isset($postData['desc']) ? trim($postData['desc']) : '',
            'status' => isset($postData['status']) ? $postData['status'] : 'pending',
            'percent' => isset($postData['percent']) ? intval($postData['percent']) : 0
        ];
        if (empty($item['name'])) {
            jsonResponse(['code' => 400, 'msg' => '请输入项目名称'], 400);
        }
        $item['percent'] = max(0, min(100, $item['percent']));
        if ($item['status'] === 'done') $item['percent'] = 100;
        $data['progressItems'][] = $item;
        writeData($data);
        jsonResponse(['code' => 0, 'msg' => '已添加', 'id' => $item['id']]);
        break;

    case 'update_progress':
        $id = isset($postData['id']) ? $postData['id'] : '';
        if (empty($id)) {
            jsonResponse(['code' => 400, 'msg' => '缺少ID'], 400);
        }
        foreach ($data['progressItems'] as $i => $p) {
            if ($p['id'] === $id) {
                $data['progressItems'][$i]['name'] = isset($postData['name']) ? trim($postData['name']) : $p['name'];
                $data['progressItems'][$i]['desc'] = isset($postData['desc']) ? trim($postData['desc']) : $p['desc'];
                $data['progressItems'][$i]['status'] = isset($postData['status']) ? $postData['status'] : $p['status'];
                $percent = isset($postData['percent']) ? intval($postData['percent']) : $p['percent'];
                $data['progressItems'][$i]['percent'] = max(0, min(100, $percent));
                if ($data['progressItems'][$i]['status'] === 'done') $data['progressItems'][$i]['percent'] = 100;
                writeData($data);
                jsonResponse(['code' => 0, 'msg' => '已更新']);
            }
        }
        jsonResponse(['code' => 404, 'msg' => '进度不存在'], 404);
        break;

    case 'delete_progress':
        $id = isset($postData['id']) ? $postData['id'] : '';
        $data['progressItems'] = array_values(array_filter($data['progressItems'] ?? [], function($p) use ($id) {
            return $p['id'] !== $id;
        }));
        writeData($data);
        jsonResponse(['code' => 0, 'msg' => '已删除']);
        break;

    // ============ 首页卡片管理 ============
    case 'get_home_cards':
        jsonResponse(['code' => 0, 'data' => $data['homeCards'] ?? []]);
        break;

    case 'save_home_cards':
        $cards = isset($postData['cards']) ? $postData['cards'] : [];
        $data['homeCards'] = $cards;
        writeData($data);
        jsonResponse(['code' => 0, 'msg' => '已保存']);
        break;

    // ============ 推荐卡片管理 ============
    case 'get_recommend_cards':
        jsonResponse(['code' => 0, 'data' => $data['recommendCards'] ?? []]);
        break;

    case 'save_recommend_cards':
        $cards = isset($postData['cards']) ? $postData['cards'] : [];
        $data['recommendCards'] = $cards;
        writeData($data);
        jsonResponse(['code' => 0, 'msg' => '已保存']);
        break;

    // 兼容旧接口
    case 'get_recommend':
        jsonResponse(['code' => 0, 'data' => $data['recommendBatches'] ?? $data['recommendCards'] ?? []]);
        break;

    case 'save_recommend':
        $batches = isset($postData['batches']) ? $postData['batches'] : (isset($postData['cards']) ? $postData['cards'] : []);
        $data['recommendBatches'] = $batches;
        $data['recommendCards'] = $batches;
        writeData($data);
        jsonResponse(['code' => 0, 'msg' => '已保存']);
        break;

    // ============ 反馈管理 ============
    case 'get_feedback':
        jsonResponse(['code' => 0, 'data' => $data['feedback'] ?? []]);
        break;

    case 'delete_feedback':
        $id = isset($postData['id']) ? $postData['id'] : '';
        $data['feedback'] = array_values(array_filter($data['feedback'] ?? [], function($f) use ($id) {
            return $f['id'] !== $id;
        }));
        writeData($data);
        jsonResponse(['code' => 0, 'msg' => '已删除']);
        break;

    // ============ 文件管理 ============
    case 'list_files':
        $uploadDir = __DIR__ . '/uploads/';
        $files = [];
        if (is_dir($uploadDir)) {
            $dir = dir($uploadDir);
            if ($dir) {
                while (false !== ($entry = $dir->read())) {
                    if ($entry === '.' || $entry === '..' || $entry === '.gitkeep') continue;
                    $fullPath = $uploadDir . $entry;
                    if (!is_file($fullPath)) continue;
                    $size = filesize($fullPath);
                    // 格式化大小
                    if ($size >= 1024 * 1024) { $sizeStr = round($size / 1024 / 1024, 2) . ' MB'; }
                    else if ($size >= 1024) { $sizeStr = round($size / 1024, 1) . ' KB'; }
                    else { $sizeStr = $size . ' B'; }
                    $base = rtrim(dirname(dirname($_SERVER['SCRIPT_NAME'])), '/');
                    $url = $base . '/admin/uploads/' . $entry;
                    $ext = strtolower(pathinfo($entry, PATHINFO_EXTENSION));
                    $isImg = in_array($ext, ['jpg','jpeg','png','gif','webp','svg','bmp','ico']);
                    $isAudio = in_array($ext, ['mp3','aac','wav','ogg','flac','m4a']);
                    $files[] = [
                        'name' => $entry,
                        'url' => $url,
                        'size' => $sizeStr,
                        'sizeBytes' => $size,
                        'mtime' => date('Y-m-d H:i:s', filemtime($fullPath)),
                        'type' => $isImg ? 'image' : ($isAudio ? 'audio' : 'other'),
                        'ext' => $ext
                    ];
                }
                $dir->close();
            }
        }
        // 按修改时间倒序（最新在上）
        usort($files, function($a, $b) { return $b['sizeBytes'] <=> $a['sizeBytes']; });
        // 计算总大小
        $totalBytes = 0;
        foreach ($files as $f) $totalBytes += $f['sizeBytes'];
        if ($totalBytes >= 1024 * 1024) { $totalSize = round($totalBytes / 1024 / 1024, 2) . ' MB'; }
        else if ($totalBytes >= 1024) { $totalSize = round($totalBytes / 1024, 1) . ' KB'; }
        else { $totalSize = $totalBytes . ' B'; }
        jsonResponse(['code' => 0, 'data' => $files, 'count' => count($files), 'totalSize' => $totalSize]);
        break;

    case 'delete_file':
        $filename = isset($postData['name']) ? basename($postData['name']) : '';
        if (!$filename) { jsonResponse(['code' => 400, 'msg' => '参数错误'], 400); break; }
        $uploadDir = __DIR__ . '/uploads/';
        $fullPath = $uploadDir . $filename;
        // 禁止路径穿越
        if (strpos($filename, '..') !== false || strpos($filename, '/') !== false || strpos($filename, '\\') !== false) {
            jsonResponse(['code' => 400, 'msg' => '非法文件名'], 400);
            break;
        }
        if (is_file($fullPath)) {
            if (unlink($fullPath)) {
                jsonResponse(['code' => 0, 'msg' => '已删除']);
            } else {
                jsonResponse(['code' => 500, 'msg' => '删除失败，权限不足'], 500);
            }
        } else {
            jsonResponse(['code' => 404, 'msg' => '文件不存在'], 404);
        }
        break;

    // ============ 重置数据 ============
    case 'reset':
        $defaults = getDefaults();
        writeData($defaults);
        jsonResponse(['code' => 0, 'msg' => '已重置为默认数据']);
        break;

    // ============ 上传文件 ============
    case 'upload':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_FILES['file'])) {
            jsonResponse(['code' => 400, 'msg' => '没有文件上传'], 400);
        }
        $file = $_FILES['file'];
        if ($file['error'] !== UPLOAD_ERR_OK) {
            jsonResponse(['code' => 400, 'msg' => '上传失败'], 400);
        }
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'mp3', 'aac', 'wav', 'ogg'];
        if (!in_array(strtolower($ext), $allowed)) {
            jsonResponse(['code' => 400, 'msg' => '不支持的文件类型'], 400);
        }
        $uploadDir = __DIR__ . '/uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        $filename = date('YmdHis') . '_' . mt_rand(1000, 9999) . '.' . $ext;
        $filepath = $uploadDir . $filename;
        if (move_uploaded_file($file['tmp_name'], $filepath)) {
            // 返回服务器相对路径，兼容项目在任意子目录下部署
            $base = dirname(dirname($_SERVER['SCRIPT_NAME']));
            $url = rtrim($base, '/') . '/admin/uploads/' . $filename;
            jsonResponse(['code' => 0, 'url' => $url]);
        }
        jsonResponse(['code' => 500, 'msg' => '保存失败'], 500);
        break;

    default:
        jsonResponse(['code' => 404, 'msg' => '未知接口'], 404);
}
