<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Glass Login</title>
    <style>
        /* 全局样式 静态背景，移除滚动动画 */
        body {
            padding: 0;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            /* 静态碎花背景，无动画 */
            background: url("back.png") center center;
            background-size: 650px;
            font-family: system-ui, -apple-system, sans-serif;
            animation: moveBackground 60s linear infinite;
        }
        @keyframes moveBackground {
    from { background-position: 0% 0%; }
    to { background-position: 0% -1500%; }
}
        .container {
            display: flex;
            justify-content: center;
            align-items: center;
            width: 100%;
        }

        /* ========== 液态玻璃核心（复刻index折射滤镜） ========== */
        .glass-login-card {
            position: relative;
            width: 340px;
            padding: 28px;
            overflow: hidden;
            border-radius: 22px;
            box-shadow: 0 8px 12px rgba(0,0,0,0.22), 0 0 24px rgba(0,0,0,0.12);
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 2.2);
        }
        .glass-login-card:hover {
            transform: translateY(-4px);
            padding: 30px;
            border-radius: 26px;
        }
        /* 折射扭曲层 核心玻璃变形效果 */
        .liquidGlass-effect {
            position: absolute;
            z-index: 0;
            inset: 0;
            backdrop-filter: blur(4px);
            filter: url(#glass-distortion);
            overflow: hidden;
            isolation: isolate;
        }
        /* 玻璃半透底色 */
        .liquidGlass-tint {
            z-index: 1;
            position: absolute;
            inset: 0;
            background: rgba(255, 255, 255, 0.28);
        }
        /* 玻璃高光描边 */
        .liquidGlass-shine {
            position: absolute;
            inset: 0;
            z-index: 2;
            overflow: hidden;
            box-shadow: inset 2px 2px 1px 0 rgba(255,255,255,0.6),
                        inset -1px -1px 1px 1px rgba(255,255,255,0.4);
        }
        /* 表单内容置顶层级 */
        .form-wrap {
            position: relative;
            z-index: 3;
        }

        /* ========== 表单样式 完整保留左侧图标 ========== */
        :root {
            --primary: #3b82f6;
            --primary-dark: #2563eb;
            --primary-light: rgba(59, 130, 246, 0.15);
            --success: #10b981;
            --text-main: #0f172a;
            --text-secondary: #334155;
            --bg-input: rgba(255,255,255,0.65);
        }
        .form-title {
            font-size: 24px;
            font-weight: 600;
            color: var(--text-main);
            margin: 0 0 26px;
            text-align: center;
            letter-spacing: -0.01em;
        }
        .input-group {
            margin-bottom: 18px;
        }
        .input-wrapper {
            position: relative;
            display: flex;
            align-items: center;
        }
        .form-input {
            width: 100%;
            height: 44px;
            padding: 0 40px;
            font-size: 14px;
            border: 1px solid rgba(226, 232, 240, 0.7);
            border-radius: 12px;
            background: var(--bg-input);
            color: var(--text-main);
            transition: all 0.2s ease;
            backdrop-filter: blur(6px);
        }
        .form-input::placeholder {
            color: var(--text-secondary);
        }
        /* 左侧图标样式 完整保留不删除 */
        .input-icon {
            position: absolute;
            left: 14px;
            width: 18px;
            height: 18px;
            color: var(--text-secondary);
            pointer-events: none;
            z-index: 4;
        }
        .password-toggle {
            position: absolute;
            right: 14px;
            display: flex;
            align-items: center;
            padding: 4px;
            background: none;
            border: none;
            color: var(--text-secondary);
            cursor: pointer;
            transition: all 0.2s ease;
            z-index: 4;
        }
        .eye-icon {
            width: 18px;
            height: 18px;
        }
        .submit-button {
            position: relative;
            width: 100%;
            height: 44px;
            margin-top: 10px;
            background: var(--primary);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            overflow: hidden;
            transition: all 0.2s ease;
        }
        .button-glow {
            position: absolute;
            inset: 0;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.25), transparent);
            transform: translateX(-100%);
            transition: transform 0.5s ease;
        }
        .form-footer {
            margin-top: 18px;
            text-align: center;
            font-size: 13px;
        }
        .login-link {
            color: var(--text-secondary);
            text-decoration: none;
            transition: all 0.2s ease;
        }
        .login-link span {
            color: var(--primary);
            font-weight: 500;
        }

        /* 交互状态 */
        .form-input:hover {
            border-color: rgba(203, 213, 225, 0.9);
        }
        .form-input:focus {
            outline: none;
            border-color: var(--primary);
            background: rgba(255,255,255,0.85);
            box-shadow: 0 0 0 4px var(--primary-light);
        }
        .password-toggle:hover {
            color: var(--primary);
            transform: scale(1.1);
        }
        .submit-button:hover {
            background: var(--primary-dark);
            transform: translateY(-1px);
            box-shadow: 0 4px 14px rgba(59, 130, 246, 0.3), 0 2px 6px rgba(59, 130, 246, 0.18);
        }
        .submit-button:hover .button-glow {
            transform: translateX(100%);
        }
        .login-link:hover {
            color: var(--text-main);
        }
        .login-link:hover span {
            color: var(--primary-dark);
        }
        .submit-button:active {
            transform: translateY(0);
            box-shadow: none;
        }
        .password-toggle:active {
            transform: scale(0.9);
        }

        /* 表单校验抖动动画 */
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-4px); }
            75% { transform: translateX(4px); }
        }
        .form-input:not(:placeholder-shown):valid {
            border-color: var(--success);
        }
        .form-input:not(:placeholder-shown):valid ~ .input-icon {
            color: var(--success);
        }
        .form-input:not(:placeholder-shown):invalid {
            border-color: #ef4444;
            animation: shake 0.2s ease-in-out;
        }
        .form-input:not(:placeholder-shown):invalid ~ .input-icon {
            color: #ef4444;
        }
    </style>
</head>
<body>
    <!-- 液态折射滤镜 必须保留 -->
    <svg style="display: none">
        <filter id="glass-distortion" x="0%" y="0%" width="100%" height="100%" filterUnits="objectBoundingBox">
            <feTurbulence type="fractalNoise" baseFrequency="0.01 0.01" numOctaves="1" seed="5" result="turbulence"/>
            <feComponentTransfer in="turbulence" result="mapped">
                <feFuncR type="gamma" amplitude="1" exponent="10" offset="0.5"/>
                <feFuncG type="gamma" amplitude="0" exponent="1" offset="0"/>
                <feFuncB type="gamma" amplitude="0" exponent="1" offset="0.5"/>
            </feComponentTransfer>
            <feGaussianBlur in="turbulence" stdDeviation="3" result="softMap"/>
            <feSpecularLighting in="softMap" surfaceScale="5" specularConstant="1" specularExponent="100" lighting-color="white" result="specLight">
                <fePointLight x="-200" y="-200" z="300"/>
            </feSpecularLighting>
            <feComposite in="specLight" operator="arithmetic" k1="0" k2="1" k3="1" k4="0" result="litImage"/>
            <feDisplacementMap in="SourceGraphic" in2="softMap" scale="150" xChannelSelector="R" yChannelSelector="G"/>
        </filter>
    </svg>

    <div class="container">
        <!-- 液态玻璃卡片容器 -->
        <div class="glass-login-card">
            <!-- 三层玻璃结构 -->
            <div class="liquidGlass-effect"></div>
            <div class="liquidGlass-tint"></div>
            <div class="liquidGlass-shine"></div>
            <!-- 表单区域 完整保留左侧svg图标 -->
            <div class="form-wrap">
                <div class="form-title">登录后台</div>
                <form>
                    <div class="input-group">
                        <div class="input-wrapper">
                            <!-- 用户名左侧图标 完整保留 -->
                            <svg fill="none" viewBox="0 0 24 24" class="input-icon">
                                <circle stroke-width="1.5" stroke="currentColor" r="4" cy="8" cx="12"></circle>
                                <path stroke-linecap="round" stroke-width="1.5" stroke="currentColor" d="M5 20C5 17.2386 8.13401 15 12 15C15.866 15 19 17.2386 19 20"></path>
                            </svg>
                            <input required placeholder="用户名" class="form-input" type="text">
                        </div>
                    </div>
                    <div class="input-group">
                        <div class="input-wrapper">
                            <!-- 密码左侧图标 完整保留 -->
                            <svg fill="none" viewBox="0 0 24 24" class="input-icon">
                                <path stroke-width="1.5" stroke="currentColor" d="M12 10V14M8 6H16C17.1046 6 18 6.89543 18 8V16C18 17.1046 17.1046 18 16 18H8C6.89543 18 6 17.1046 6 16V8C6 6.89543 6.89543 6 8 6Z"></path>
                            </svg>
                            <input required placeholder="密码" class="form-input" type="password" id="pwd">
                            <button class="password-toggle" type="button" onclick="togglePwd()">
                                <svg fill="none" viewBox="0 0 24 24" class="eye-icon">
                                    <path stroke-width="1.5" stroke="currentColor" d="M2 12C2 12 5 5 12 5C19 5 22 12 22 12C22 12 19 19 12 19C5 19 2 12 2 12Z"></path>
                                    <circle stroke-width="1.5" stroke="currentColor" r="3" cy="12" cx="12"></circle>
                                </svg>
                            </button>
                        </div>
                    </div>
                    <button class="submit-button" type="submit">
                        <span class="button-text">登录系统</span>
                        <div class="button-glow"></div>
                    </button>
                </form>
                <div class="form-footer">
                    <a class="login-link" href="#">忘记密码？<span>点击重置</span></a>
                </div>
            </div>
        </div>
    </div>

    <script>
        // 密码显示隐藏切换功能
        function togglePwd(){
            const pwd = document.getElementById('pwd');
            pwd.type = pwd.type === 'password' ? 'text' : 'password';
        }
        // 登录逻辑 - 对接后端API
        document.querySelector('.glass-login-card form').addEventListener('submit',function(e){
            e.preventDefault();
            const inputs=this.querySelectorAll('.form-input');
            const user=inputs[0].value.trim();
            const pass=inputs[1].value;
            if(!user||!pass){alert('请输入用户名和密码');return;}
            fetch('api.php?action=login',{
                method:'POST',
                headers:{'Content-Type':'application/json'},
                body:JSON.stringify({username:user,password:pass})
            })
            .then(function(res){return res.json();})
            .then(function(data){
                if(data.code===0){
                    localStorage.setItem('nav_admin_logged_in','1');
                    window.location.href='index.php';
                }else{
                    alert(data.msg||'用户名或密码错误');
                    inputs[0].style.animation='shake .2s ease-in-out';
                    setTimeout(function(){inputs[0].style.animation='';},300);
                }
            })
            .catch(function(){
                alert('网络错误，请检查服务器是否支持PHP');
                // 降级到本地验证
                var acc=JSON.parse(localStorage.getItem('nav_admin_account')||'{"username":"admin","password":"123456"}');
                if(acc.username===user&&acc.password===pass){
                    localStorage.setItem('nav_admin_logged_in','1');
                    window.location.href='index.php';
                }else{
                    alert('用户名或密码错误');
                }
            });
        });
        // 自动检查登录状态：必须 PHP Session 确认已登录才跳
        (function(){
            if(localStorage.getItem('nav_admin_logged_in')==='1'){
                fetch('api.php?action=check_login',{credentials:'include'})
                    .then(function(r){return r.json();})
                    .then(function(d){
                        if(d.code===0){
                            // 后端确认已登录，才跳转到主页
                            window.location.href='index.php';
                        }else{
                            // 后端说未登录 → 清除本地标记，停留在登录页
                            localStorage.removeItem('nav_admin_logged_in');
                        }
                    })
                    .catch(function(){
                        // fetch 失败（服务器/网络错误）：不跳，停留在登录页防止循环
                        localStorage.removeItem('nav_admin_logged_in');
                    });
            }
        })();
    </script>
</body>
</html>