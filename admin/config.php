<?php
$config = array(
    'admin_user' => 'admin',//管理员用户名
    'admin_password' => '123456',//管理员密码
    'web_profile' => '导航网二改',//网站介绍
    'web_title' => '导航网二改',//网站标题
    'web_logo' => 'logo.png',//网站logo
    'data_view' => '0',//是否显示查看数据
    'data_announcement' => '0',//是否显示公告数据
    'data_card' => '0',//是否显示卡片数据
    'data_feedback' => '0',//是否显示反馈数据
    'data_music' => '0',//是否显示音乐数据
    'data_headline' => '0',//是否显示头条数据
    'data_setting' => '0',//是否显示设置数据
    'setting_music' => true,//是否显示音乐数据
    'setting_progress' => true,//是否显示进度数据
    'setting_headline' => true,//是否显示头条数据
    'setting_clock' => true,//是否显示时钟数据
    'setting_saying' => true,//是否显示说说数据
    'announcement_title' => '公告',//公告标题
    'announcement_content' => '这是公告内容',//公告内容
    'announcement_url' => 'https://www.baidu.com',//公告链接
);

/*
 * 音乐播放列表
 * 支持多首音乐，播放器会自动循环播放
 * 在后台管理 -> 音乐管理 中添加/管理音乐
 * 这里的配置作为初始默认值（当后台还未添加音乐时使用）
 */
$music_playlist = array(
    array(
        'music_url' => 'https://file.kxlove.top/view.php/41110a6125f7624b2376677cef0d82aa.aac',//音乐url
        'music_title' => 'Liquid Dreams',//音乐标题
        'music_artist' => 'Glass FM',//音乐艺术家
        'music_cover' => 'https://file.kxlove.top/view.php/a2d2a15fdb941e06e02634ab286fd71d.png',//音乐封面
    ),
    // 继续添加更多音乐...
    // array(
    //     'music_url' => 'https://example.com/song2.mp3',
    //     'music_title' => '歌曲名',
    //     'music_artist' => '艺术家',
    //     'music_cover' => 'https://example.com/cover.jpg'
    // ),
);
$headline_config = array(
    // 在后台「头条」管理中添加，此处留空不显示默认数据
    );
    $progress_config = array(
    // 在后台「进度」管理中添加，此处留空不显示默认数据
    );
