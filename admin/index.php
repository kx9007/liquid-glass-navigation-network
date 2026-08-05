<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>液态玻璃后台</title>
    <style>
        /* ============================================
           CSS 变量 & 主题
           ============================================ */
        :root {
            --primary: #3b82f6;
            --primary-dark: #2563eb;
            --primary-light: rgba(59, 130, 246, .20);
            --success: #10b981;
            --danger: #ef4444;
            --warning: #f59e0b;
            --text-main: #0f172a;
            --text-secondary: #334155;
            --text-muted: #64748b;
            --glass-bg: rgba(255, 255, 255, .18);
            --glass-border: rgba(255, 255, 255, .35);
            --glass-shadow: rgba(0, 0, 0, .08);
            --glass-text: #0f172a;
            --card-radius: 16px;
            --pill-bg: rgba(255, 255, 255, .70);
            --pill-shadow: 0 4px 12px rgba(0, 0, 0, .06), 0 1px 2px rgba(0, 0, 0, .04), inset 0 1px 1px rgba(255, 255, 255, .8);
            --icon-color: rgba(0, 0, 0, .45);
            --icon-active: rgba(0, 0, 0, .92);
            --divider: rgba(0, 0, 0, .10);
            --reflection-start: rgba(255, 255, 255, .50);
            --reflection-end: rgba(255, 255, 255, 0);
            --glare-color: rgba(255, 255, 255, .40);
            --bg-color: #e5e5ea;
            --blob-1: #ff2a5f;
            --blob-2: #007aff;
            --blob-3: #ff9500;
            --blob-opacity: .50;
            --toast-bg: rgba(15, 23, 42, .88);
            --max-width: 640px;
            --switch-on: #3b82f6;
            --switch-off: rgba(255, 255, 255, .20);
        }

        /* 桌面端限宽布局 */
        @media(min-width:768px) {
            :root { --max-width: 960px; }
        }
        @media(min-width:1024px) {
            :root { --max-width: 1200px; }
        }
        @media(min-width:1440px) {
            :root { --max-width: 1400px; }
        }

        [data-theme="dark"] {
            --bg-color: #0a0a0f;
            --blob-1: #bf5af2;
            --blob-2: #0a84ff;
            --blob-3: #ff375f;
            --blob-opacity: .40;
            --glass-bg: rgba(30, 30, 40, .50);
            --glass-border: rgba(255, 255, 255, .10);
            --glass-shadow: rgba(0, 0, 0, .60);
            --text-main: #f1f5f9;
            --text-secondary: #cbd5e1;
            --text-muted: #94a3b8;
            --pill-bg: rgba(60, 60, 70, .75);
            --pill-shadow: 0 4px 12px rgba(0, 0, 0, .35), 0 1px 2px rgba(0, 0, 0, .18), inset 0 1px 1px rgba(255, 255, 255, .12);
            --icon-color: rgba(255, 255, 255, .45);
            --icon-active: #ffffff;
            --divider: rgba(255, 255, 255, .10);
            --reflection-start: rgba(255, 255, 255, .10);
            --reflection-end: rgba(255, 255, 255, 0);
            --glare-color: rgba(255, 255, 255, .10);
            --glass-text: #f1f5f9;
            --toast-bg: rgba(30, 30, 40, .92);
            --switch-off: rgba(255, 255, 255, .08);
        }

        /* ============================================
           全局重置 & 背景
           ============================================ */
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        html,
        body {
            height: 100%;
        }
        body {
            font-family: -apple-system, BlinkMacSystemFont, "PingFang SC", "Microsoft YaHei", system-ui, sans-serif;
            min-height: 100vh;
            color: var(--glass-text);
            padding-top: 78px;
            padding-bottom: 90px;
            -webkit-tap-highlight-color: transparent;
            background: var(--bg-color);
            transition: background .8s ease;
            overflow-x: hidden;
        }

        .bg-mesh {
            position: fixed;
            inset: 0;
            z-index: 0;
            background: var(--bg-color);
            transition: background .8s ease;
            overflow: hidden;
            pointer-events: none;
        }
        .blob {
            position: absolute;
            border-radius: 50%;
            filter: blur(90px);
            opacity: var(--blob-opacity);
            animation: float 20s infinite alternate cubic-bezier(.45, .05, .55, .95);
            will-change: transform;
            transition: background .8s ease, opacity .8s ease;
        }
        .blob-1 {
            width: 50vw;
            height: 50vw;
            top: -10%;
            left: -10%;
            background: var(--blob-1);
            animation-delay: 0s;
        }
        .blob-2 {
            width: 45vw;
            height: 45vw;
            bottom: -10%;
            right: -10%;
            background: var(--blob-2);
            animation-delay: -5s;
        }
        .blob-3 {
            width: 35vw;
            height: 35vw;
            top: 30%;
            left: 40%;
            background: var(--blob-3);
            animation-delay: -10s;
        }
        @keyframes float {
            0% {
                transform: translate(0, 0) scale(1) rotate(0deg);
            }
            33% {
                transform: translate(5%, 10%) scale(1.05) rotate(5deg);
            }
            66% {
                transform: translate(-5%, 5%) scale(.95) rotate(-5deg);
            }
            100% {
                transform: translate(0, -10%) scale(1.1) rotate(0deg);
            }
        }

        svg.liquid-filter {
            display: none;
            position: absolute;
            width: 0;
            height: 0;
        }

        /* ============================================
           🎛️ 灵动岛控制中心 (iOS风格)
           ============================================ */
        .control-island {
            position: fixed;
            top: 12px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 200;
            cursor: pointer;
            width: min(320px, calc(100vw - 28px));
            -webkit-tap-highlight-color: transparent;
            transition: opacity .4s ease, transform .4s ease;
        }
        .control-island.hidden {
            opacity: 0;
            pointer-events: none;
            transform: translateX(-50%) translateY(-20px);
        }
        .control-container {
            position: relative;
            display: flex;
            align-items: center;
            min-height: 48px;
            padding: 6px 14px 6px 14px;
            border-radius: 99px;
            gap: 8px;
            background: rgba(255, 255, 255, .22);
            backdrop-filter: blur(2px) url(#liquid_glass_filter);
            -webkit-backdrop-filter: blur(2px) url(#liquid_glass_filter);
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, .40), inset 0 -1px 0 rgba(255, 255, 255, .15), inset 6px 6px 16px rgba(255, 255, 255, .08), 0 10px 24px rgba(0, 0, 0, .30);
            overflow: hidden;
            transition: min-height .5s cubic-bezier(.34, 1.2, .64, 1), padding .5s cubic-bezier(.34, 1.2, .64, 1), border-radius .5s cubic-bezier(.34, 1.2, .64, 1), box-shadow .5s ease, transform .35s ease;
            justify-content: space-between;
        }
        .control-container::before {
            content: '';
            position: absolute;
            top: 1px;
            left: 1px;
            right: 1px;
            height: 44%;
            border-radius: inherit;
            background: linear-gradient(180deg, var(--reflection-start) 0%, var(--reflection-end) 100%);
            pointer-events: none;
            z-index: 2;
            transition: background .5s ease;
        }
        .control-island.expanded .control-container {
            min-height: 300px;
            padding: 16px;
            border-radius: 28px;
            align-items: flex-start;
        }
        .control-compact {
            position: relative;
            display: flex;
            align-items: center;
            gap: 6px;
            width: 100%;
            z-index: 3;
            transition: opacity .25s ease, transform .35s ease;
            justify-content: space-between;
        }
        .control-compact .ctrl-label {
            font-size: 13px;
            font-weight: 600;
            color: var(--icon-active);
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .control-compact .ctrl-hint {
            font-size: 11px;
            color: var(--icon-color);
            opacity: .7;
        }
        .control-panel {
            position: absolute;
            inset: 16px;
            z-index: 3;
            display: flex;
            flex-direction: column;
            gap: 6px;
            opacity: 0;
            transform: translateY(18px) scale(.96);
            pointer-events: none;
            transition: opacity .3s ease, transform .45s cubic-bezier(.34, 1.2, .64, 1);
            width: calc(100% - 32px);
            overflow-y: auto;
            max-height: 260px;
        }
        .control-panel::-webkit-scrollbar {
            width: 3px;
        }
        .control-panel::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, .25);
            border-radius: 99px;
        }
        .control-island.expanded .control-compact {
            opacity: 0;
            transform: translateY(-10px) scale(.96);
            pointer-events: none;
        }
        .control-island.expanded .control-panel {
            opacity: 1;
            transform: translateY(0) scale(1);
            pointer-events: auto;
        }

        /* iOS 风格开关网格 */
        .ctrl-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 8px;
            width: 100%;
        }
        .ctrl-grid .ctrl-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 10px 6px 8px;
            border-radius: 14px;
            background: var(--switch-off);
            border: 1px solid rgba(255, 255, 255, .06);
            transition: all .3s cubic-bezier(.34, 1.2, .64, 1);
            cursor: pointer;
            -webkit-tap-highlight-color: transparent;
            min-height: 68px;
            position: relative;
            user-select: none;
        }
        .ctrl-grid .ctrl-item.active {
            background: var(--switch-on);
            border-color: var(--switch-on);
            box-shadow: 0 4px 14px rgba(59, 130, 246, .35);
        }
        .ctrl-grid .ctrl-item .ctrl-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 26px;
            height: 26px;
            margin-bottom: 4px;
            opacity: .7;
            transition: opacity .3s;
        }
        .ctrl-grid .ctrl-item .ctrl-icon svg {
            width: 22px;
            height: 22px;
        }
        .ctrl-grid .ctrl-item.active .ctrl-icon {
            opacity: 1;
        }
        .ctrl-grid .ctrl-item .ctrl-name {
            font-size: 10px;
            font-weight: 600;
            color: var(--icon-color);
            text-align: center;
            line-height: 1.2;
            transition: color .3s;
        }
        .ctrl-grid .ctrl-item.active .ctrl-name {
            color: #fff;
        }
        .ctrl-grid .ctrl-item:active {
            transform: scale(.94);
        }

        @media(max-width:480px) {
            .control-island {
                top: 10px;
                width: min(290px, calc(100vw - 20px));
            }
            .control-island.expanded .control-container {
                min-height: 280px;
                border-radius: 24px;
                padding: 14px;
            }
            .control-panel {
                inset: 14px;
                max-height: 240px;
            }
            .ctrl-grid {
                gap: 6px;
            }
            .ctrl-grid .ctrl-item {
                min-height: 60px;
                padding: 8px 4px 6px;
                border-radius: 12px;
            }
            .ctrl-grid .ctrl-item .ctrl-icon {
                font-size: 16px;
            }
            .ctrl-grid .ctrl-item .ctrl-name {
                font-size: 9px;
            }
            body {
                padding-top: 70px;
            }
        }
        @media(max-width:380px) {
            .control-island {
                top: 6px;
                width: calc(100vw - 14px);
            }
            .control-container {
                min-height: 42px;
                padding: 4px 10px;
            }
            .control-island.expanded .control-container {
                min-height: 260px;
                padding: 12px;
            }
            .ctrl-grid .ctrl-item {
                min-height: 52px;
            }
            body {
                padding-top: 60px;
            }
        }

        /* ============================================
           主容器 - 更窄更紧凑
           ============================================ */
        .container {
            max-width: var(--max-width);
            margin: 0 auto;
            padding: 8px 12px 12px;
            position: relative;
            z-index: 1;
        }
        @media(max-width:768px) {
            .container {
                padding: 8px 14px 14px;
            }
        }
        @media(max-width:480px) {
            .container {
                padding: 6px 12px 12px;
            }
        }

        .glass-card {
            position: relative;
            padding: 14px 14px;
            border-radius: var(--card-radius);
            background: var(--glass-bg);
            backdrop-filter: blur(20px) saturate(180%);
            -webkit-backdrop-filter: blur(20px) saturate(180%);
            border: 1px solid var(--glass-border);
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, .35), 0 8px 28px var(--glass-shadow);
            overflow: hidden;
            transition: background .5s ease, border-color .5s ease, box-shadow .5s ease;
            margin-bottom: 12px;
        }
        .glass-card::before {
            content: "";
            position: absolute;
            inset: 0;
            z-index: 0;
            pointer-events: none;
            border-radius: var(--card-radius);
            background: linear-gradient(135deg, rgba(255, 255, 255, .20) 0%, transparent 50%);
        }
        .glass-card>* {
            position: relative;
            z-index: 1;
        }
        @media(max-width:600px) {
            .glass-card {
                padding: 10px 10px;
            }
        }
        @media(max-width:400px) {
            .glass-card {
                padding: 8px 8px;
            }
        }

        .view {
            display: none;
            animation: fadeUp .25s ease;
        }
        .view.active {
            display: block;
        }

        /* 桌面端：所有视图限宽，居中显示 */
        @media(min-width:1024px) {
            .view {
                max-width: 100%;
                margin-left: auto;
                margin-right: auto;
            }
        }
        @media(min-width:1440px) {
            .view {
                max-width: 100%;
            }
        }
        @keyframes fadeUp {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .section-title {
            font-size: 14px;
            font-weight: 600;
            margin: 0 0 10px;
            color: var(--text-main);
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .section-title svg {
            width: 16px;
            height: 16px;
            color: var(--primary);
        }

        /* ============================================
           仪表盘 - 布局
           ============================================ */
        .dashboard-row {
            display: grid;
            grid-template-columns: 1fr;
            gap: 14px;
            margin-bottom: 14px;
        }
        @media(min-width:1024px) {
            .dashboard-row {
                grid-template-columns: 1fr 1fr;
                gap: 16px;
            }
        }
        @media(min-width:1440px) {
            .dashboard-row {
                grid-template-columns: 1fr 1fr;
            }
        }

        /* ============================================
           仪表盘 - 数据卡片 (缩小)
           ============================================ */
        .stat-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 8px;
            margin-bottom: 0;
        }
        .stat-card {
            padding: 10px 6px;
            text-align: center;
            cursor: default;
            min-height: 88px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 3px;
        }
        .stat-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 3px;
            width: 32px;
            height: 32px;
            border-radius: 10px;
            background: rgba(255, 255, 255, .15);
            color: var(--primary);
        }
        .stat-icon svg {
            width: 18px;
            height: 18px;
            stroke: currentColor;
            stroke-width: 2;
            fill: none;
        }
        .stat-num {
            font-size: 20px;
            font-weight: 700;
            line-height: 1.1;
            color: var(--text-main);
        }
        .stat-label {
            font-size: 11px;
            color: var(--text-muted);
            margin-top: 1px;
        }
        @media(max-width:600px) {
            .stat-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 8px;
            }
            .stat-card {
                padding: 10px 6px;
                min-height: 84px;
            }
            .stat-num {
                font-size: 18px;
            }
            .stat-label {
                font-size: 10px;
            }
            .stat-icon {
                width: 30px;
                height: 30px;
            }
            .stat-icon svg {
                width: 17px;
                height: 17px;
            }
        }
        @media(min-width:1024px) {
            .stat-grid {
                gap: 10px;
            }
            .stat-card {
                padding: 12px 6px;
                min-height: 96px;
            }
            .stat-num {
                font-size: 22px;
            }
            .stat-icon {
                width: 34px;
                height: 34px;
            }
            .stat-icon svg {
                width: 19px;
                height: 19px;
            }
        }

        .chart-card {
            padding: 12px 14px;
        }
        .chart-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 8px;
            flex-wrap: wrap;
            gap: 4px;
        }
        .chart-title {
            font-size: 12px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 4px;
            color: var(--text-main);
        }
        .chart-tabs {
            display: flex;
            gap: 3px;
            background: rgba(255, 255, 255, .20);
            border-radius: 6px;
            padding: 2px;
        }
        .chart-tab {
            padding: 3px 8px;
            font-size: 10px;
            color: var(--text-muted);
            cursor: pointer;
            border-radius: 5px;
            transition: all .2s;
        }
        .chart-tab.active {
            background: var(--primary);
            color: #fff;
        }
        #visitChart {
            width: 100%;
            height: 180px;
        }
        @media(max-width:480px) {
            #visitChart {
                height: 150px;
            }
        }

        /* ============================================
           表单 - 更紧凑
           ============================================ */
        .form-group {
            margin-bottom: 8px;
        }
        .form-label {
            display: block;
            font-size: 11px;
            font-weight: 600;
            margin-bottom: 2px;
            color: var(--text-secondary);
        }
        .form-row {
            display: flex;
            gap: 10px;
            margin-bottom: 8px;
        }
        .form-row .form-group {
            flex: 1;
            margin-bottom: 0;
        }
        @media(max-width:420px) {
            .form-row {
                flex-direction: column;
                gap: 8px;
            }
        }

        /* ============================================
           管理面板 - 桌面端左右分栏
           ============================================ */
        .manage-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 12px;
        }
        @media(min-width:900px) {
            .manage-grid {
                grid-template-columns: 1fr 1.2fr;
                gap: 16px;
            }
        }
        @media(min-width:1200px) {
            .manage-grid {
                grid-template-columns: 1fr 1.5fr;
            }
        }

        .glass-input {
            width: 100%;
            height: 34px;
            padding: 0 10px;
            font-size: 12px;
            border: 1px solid rgba(226, 232, 240, .4);
            border-radius: 8px;
            background: rgba(255, 255, 255, .40);
            color: var(--text-main);
            transition: all .2s ease;
            backdrop-filter: blur(6px);
        }
        .glass-input:focus {
            outline: none;
            border-color: var(--primary);
            background: rgba(255, 255, 255, .65);
            box-shadow: 0 0 0 3px var(--primary-light);
        }
        .glass-input::placeholder {
            color: var(--text-muted);
        }
        .glass-textarea {
            width: 100%;
            height: 25vh;
            padding: 6px 10px;
            font-size: 12px;
            border: 1px solid rgba(226, 232, 240, .4);
            border-radius: 8px;
            background: rgba(255, 255, 255, .40);
            color: var(--text-main);
            resize: vertical;
            min-height: 56px;
            transition: all .2s ease;
            backdrop-filter: blur(6px);
            font-family: inherit;
        }
        .glass-textarea:focus {
            outline: none;
            border-color: var(--primary);
            background: rgba(255, 255, 255, .65);
            box-shadow: 0 0 0 3px var(--primary-light);
        }
        select.glass-input {
            appearance: auto;
            font-size: 12px;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 4px;
            height: 32px;
            padding: 0 12px;
            font-size: 12px;
            font-weight: 500;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: all .2s ease;
            white-space: nowrap;
        }
        .btn:active {
            transform: scale(.96);
        }
        .btn-primary {
            background: var(--primary);
            color: #fff;
        }
        .btn-primary:hover {
            background: var(--primary-dark);
            box-shadow: 0 4px 12px rgba(59, 130, 246, .3);
        }
        .btn-secondary {
            background: rgba(255, 255, 255, .40);
            color: var(--text-main);
            border: 1px solid rgba(255, 255, 255, .5);
        }
        .btn-secondary:hover {
            background: rgba(255, 255, 255, .65);
        }
        .btn-danger {
            background: var(--danger);
            color: #fff;
        }
        .btn-danger:hover {
            background: #dc2626;
        }
        .btn-success {
            background: var(--success);
            color: #fff;
        }
        .btn-success:hover {
            background: #059669;
        }
        .btn-sm {
            height: 26px;
            padding: 0 7px;
            font-size: 10px;
            border-radius: 6px;
        }
        .btn-block {
            width: 100%;
        }

        /* ============================================
           图片上传
           ============================================ */
        .img-upload {
            display: flex;
            align-items: center;
            gap: 5px;
            flex-wrap: wrap;
        }
        .img-upload .glass-input {
            flex: 1 1 120px;
            min-width: 80px;
            height: 32px;
            font-size: 11px;
            padding: 0 8px;
        }
        .img-upload .btn {
            height: 32px;
            padding: 0 10px;
            font-size: 11px;
        }
        .img-upload .btn svg {
            width: 12px;
            height: 12px;
        }
        .img-preview-wrap {
            position: relative;
            display: inline-block;
            flex-shrink: 0;
        }
        .img-preview {
            width: 32px;
            height: 32px;
            border-radius: 6px;
            object-fit: cover;
            border: 1px solid rgba(255, 255, 255, .3);
            background: rgba(255, 255, 255, .15);
            display: none;
            transition: opacity .2s;
            cursor: pointer;
        }
        .img-preview.show {
            display: block;
        }
        .img-preview-wrap .del-img-btn {
            position: absolute;
            top: -5px;
            right: -5px;
            width: 16px;
            height: 16px;
            border-radius: 50%;
            background: var(--danger);
            color: #fff;
            border: 2px solid var(--glass-bg);
            font-size: 10px;
            line-height: 12px;
            text-align: center;
            cursor: pointer;
            display: none;
            transition: transform .2s;
            z-index: 5;
        }
        .img-preview-wrap .del-img-btn:hover {
            transform: scale(1.15);
        }
        .img-preview-wrap .del-img-btn.show {
            display: block;
        }
        .img-preview-wrap .del-img-btn svg {
            width: 10px;
            height: 10px;
            stroke: currentColor;
            stroke-width: 2.5;
            fill: none;
            vertical-align: middle;
            margin-top: -1px;
        }
        @media(max-width:600px) {
            .img-upload {
                flex-direction: column;
                align-items: stretch;
            }
            .img-upload .glass-input {
                width: 100%;
            }
            .img-upload .btn {
                width: 100%;
            }
            .img-preview-wrap {
                align-self: flex-start;
            }
        }

        /* ============================================
           列表
           ============================================ */
        .list-wrap {
            overflow-x: auto;
            margin-top: 2px;
        }
        .data-list {
            width: 100%;
            min-width: 380px;
            border-collapse: separate;
            border-spacing: 0;
            color: var(--text-main);
            table-layout: fixed;
            font-size: 12px;
        }
        .data-list th {
            text-align: left;
            padding: 5px 6px;
            font-size: 10px;
            background: rgba(255, 255, 255, .12);
            color: var(--text-muted);
            font-weight: 600;
            white-space: nowrap;
        }
        .data-list td {
            padding: 5px 6px;
            font-size: 11px;
            border-top: 1px solid rgba(255, 255, 255, .12);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .data-list tr:hover td {
            background: rgba(255, 255, 255, .06);
        }
        .data-list .col-actions {
            white-space: nowrap;
            width: 100px;
            text-align: right;
        }
        .data-list .col-actions .btn {
            margin-right: 2px;
        }
        .data-list .col-actions .btn:last-child {
            margin-right: 0;
        }
        .data-list th:nth-child(1),
        .data-list td:nth-child(1) {
            width: 18%;
        }
        .data-list th:nth-child(2),
        .data-list td:nth-child(2) {
            width: 36%;
        }
        .data-list th:nth-child(3),
        .data-list td:nth-child(3) {
            width: 16%;
        }
        .data-list th:nth-child(4),
        .data-list td:nth-child(4) {
            width: 30%;
        }

        .empty-state {
            text-align: center;
            padding: 20px 12px;
            color: var(--text-muted);
            font-size: 12px;
        }

        .thumb {
            width: 26px;
            height: 26px;
            border-radius: 5px;
            object-fit: cover;
            border: 1px solid rgba(255, 255, 255, .20);
        }

        /* ============================================
           进度条
           ============================================ */
        .progress-bar {
            height: 4px;
            border-radius: 3px;
            background: rgba(255, 255, 255, .20);
            overflow: hidden;
            margin: 3px 0;
        }
        .progress-fill {
            height: 100%;
            border-radius: 3px;
            transition: width .4s ease;
        }
        .progress-fill.done {
            background: linear-gradient(90deg, #34c759, #30d158);
        }
        .progress-fill.progress {
            background: linear-gradient(90deg, #0a84ff, #5ac8fa);
        }
        .progress-fill.warning {
            background: linear-gradient(90deg, #ff9500, #ffb340);
        }
        .progress-fill.pending {
            background: linear-gradient(90deg, #8e8e93, #aeaeb2);
        }

        .status-badge {
            display: inline-block;
            padding: 1px 6px;
            border-radius: 99px;
            font-size: 9px;
            font-weight: 600;
        }
        .status-done {
            background: rgba(52, 199, 89, .15);
            color: #16a34a;
        }
        .status-progress {
            background: rgba(10, 122, 255, .15);
            color: #2563eb;
        }
        .status-warning {
            background: rgba(255, 149, 0, .15);
            color: #d97706;
        }
        .status-pending {
            background: rgba(142, 142, 147, .15);
            color: #6b7280;
        }

        /* ============================================
           模态框
           ============================================ */
        .modal-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, .40);
            z-index: 1000;
            display: none;
            align-items: center;
            justify-content: center;
            padding: 16px;
            animation: fadeIn .2s;
        }
        .modal-overlay.show {
            display: flex;
        }
        .modal {
            width: 100%;
            max-width: 400px;
            max-height: 85vh;
            overflow-y: auto;
            animation: scaleIn .25s cubic-bezier(.34, 1.4, .64, 1);
        }
        .modal h3 {
            font-size: 14px;
            font-weight: 700;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 6px;
            color: var(--text-main);
        }
        .modal-actions {
            display: flex;
            gap: 8px;
            margin-top: 12px;
        }
        .modal-actions .btn {
            flex: 1;
        }
        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }
        @keyframes scaleIn {
            from {
                opacity: 0;
                transform: scale(.92);
            }
            to {
                opacity: 1;
                transform: scale(1);
            }
        }
        @keyframes spin { to { transform: rotate(360deg); } }

        /* 文件卡片 - 移动端优化 */
        .file-card { transition: transform 0.25s ease, box-shadow 0.25s ease; }
        .file-card:hover { transform: translateY(-2px); }
        .file-card img { transition: transform 0.35s ease; }
        .file-card:hover img { transform: scale(1.05); }

        /* 移动端：文件网格适配 */
        @media (max-width: 600px) {
            #fileGrid { grid-template-columns: repeat(auto-fill, minmax(125px, 1fr)) !important; gap: 8px !important; }
        }

        /* ============================================
           Toast
           ============================================ */
        .toast {
            position: fixed;
            bottom: 110px;
            left: 50%;
            transform: translateX(-50%);
            padding: 6px 18px;
            border-radius: 99px;
            background: var(--toast-bg);
            color: #fff;
            backdrop-filter: blur(10px);
            font-size: 12px;
            font-weight: 500;
            z-index: 9999;
            opacity: 0;
            transition: opacity .3s, transform .3s;
            pointer-events: none;
            white-space: nowrap;
        }
        .toast.show {
            opacity: 1;
            transform: translateX(-50%) translateY(-4px);
        }

        /* ============================================
           🌟 液态玻璃底部导航 (更紧凑)
           ============================================ */
        .liquid-nav {
            position: fixed;
            bottom: 14px;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            align-items: center;
            padding: 5px 8px;
            border-radius: 99px;
            background: rgba(255, 255, 255, .12);
            backdrop-filter: blur(2px) url(#liquid_glass_filter);
            -webkit-backdrop-filter: blur(2px) url(#liquid_glass_filter);
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, .40), inset 0 -1px 0 rgba(255, 255, 255, .15), inset 6px 6px 16px rgba(255, 255, 255, .08), 0 10px 24px rgba(0, 0, 0, .30);
            z-index: 100;
            transition: all .5s ease;
            gap: 2px;
            max-width: calc(100vw - 20px);
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            scrollbar-width: none;
        }
        .liquid-nav::-webkit-scrollbar {
            display: none;
        }
        .liquid-nav::before {
            content: '';
            position: absolute;
            top: 1px;
            left: 1px;
            right: 1px;
            height: 44%;
            border-radius: 99px 99px 20px 20px / 99px 99px 10px 10px;
            background: linear-gradient(180deg, var(--reflection-start) 0%, var(--reflection-end) 100%);
            pointer-events: none;
            z-index: 6;
            transition: background .5s ease;
        }
        .liquid-glare-container {
            position: absolute;
            inset: 0;
            border-radius: 99px;
            overflow: hidden;
            pointer-events: none;
            z-index: 5;
        }
        .liquid-glare {
            position: absolute;
            inset: 0;
            opacity: 0;
            transition: opacity .3s ease;
            background: radial-gradient(circle 70px at var(--x, 50%) var(--y, 50%), var(--glare-color) 0%, transparent 100%);
            mix-blend-mode: overlay;
        }
        .liquid-nav:hover .liquid-glare {
            opacity: .25;
        }

        .nav-items {
            position: relative;
            display: flex;
            gap: 1px;
            z-index: 3;
            align-items: center;
        }
        .active-pill {
            position: absolute;
            top: 0;
            left: 0;
            height: 42px;
            background: var(--pill-bg);
            border-radius: 99px;
            box-shadow: var(--pill-shadow);
            transition: transform .45s cubic-bezier(.34, 1.2, .64, 1), width .45s cubic-bezier(.34, 1.2, .64, 1), background .45s ease, box-shadow .45s ease;
            z-index: 1;
        }
        .nav-btn {
            position: relative;
            background: transparent;
            border: none;
            padding: 0 14px;
            height: 42px;
            border-radius: 99px;
            font-family: inherit;
            font-size: 12px;
            font-weight: 600;
            color: var(--icon-color);
            cursor: pointer;
            -webkit-tap-highlight-color: transparent;
            transition: color .3s ease;
            outline: none;
            z-index: 2;
            white-space: nowrap;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .nav-btn .btn-content {
            display: flex;
            align-items: center;
            gap: 4px;
            pointer-events: none;
            transition: transform .2s cubic-bezier(.32, .72, 0, 1);
        }
        .nav-btn:active .btn-content {
            transform: scale(.92);
        }
        .nav-btn.active {
            color: var(--icon-active);
        }
        .nav-btn svg {
            flex-shrink: 0;
            width: 18px;
            height: 18px;
        }

        .nav-divider {
            width: 1px;
            height: 24px;
            background: var(--divider);
            margin: 0 3px;
            z-index: 3;
            flex-shrink: 0;
            transition: background .5s ease;
        }

        .theme-btn {
            position: relative;
            background: transparent;
            border: none;
            width: 42px;
            height: 42px;
            border-radius: 50%;
            color: var(--icon-color);
            cursor: pointer;
            -webkit-tap-highlight-color: transparent;
            z-index: 3;
            outline: none;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: color .3s ease;
            flex-shrink: 0;
        }
        .theme-btn:hover,
        .theme-btn:active {
            color: var(--icon-active);
        }
        .theme-icon-wrapper {
            position: relative;
            width: 22px;
            height: 22px;
            pointer-events: none;
            transition: transform .2s cubic-bezier(.32, .72, 0, 1);
        }
        .theme-btn:active .theme-icon-wrapper {
            transform: scale(.8);
        }
        .theme-icon-wrapper svg {
            position: absolute;
            top: 0;
            left: 0;
            transition: transform .45s cubic-bezier(.34, 1.2, .64, 1), opacity .35s ease;
            stroke-width: 2.2;
        }
        .sun {
            opacity: 1;
            transform: rotate(0deg) scale(1);
        }
        .moon {
            opacity: 0;
            transform: rotate(-90deg) scale(0);
        }
        [data-theme="dark"] .sun {
            opacity: 0;
            transform: rotate(90deg) scale(0);
        }
        [data-theme="dark"] .moon {
            opacity: 1;
            transform: rotate(0deg) scale(1);
        }

        @media(max-width:600px) {
            .liquid-nav {
                padding: 7px 10px;
                bottom: 14px;
                gap: 2px;
            }
            .nav-btn {
                padding: 0 12px;
                height: 46px;
                font-size: 12px;
            }
            .nav-btn .btn-content span {
                display: none !important;
            }
            .nav-btn svg {
                width: 24px;
                height: 24px;
            }
            .active-pill {
                height: 46px;
            }
            .theme-btn {
                width: 46px;
                height: 46px;
            }
            .nav-divider {
                height: 28px;
            }
        }
        @media(max-width:400px) {
            .liquid-nav {
                padding: 6px 8px;
            }
            .nav-btn {
                padding: 0 10px;
                height: 42px;
            }
            .nav-btn svg {
                width: 22px;
                height: 22px;
            }
            .active-pill {
                height: 42px;
            }
            .theme-btn {
                width: 42px;
                height: 42px;
            }
            .theme-icon-wrapper {
                width: 20px;
                height: 20px;
            }
        }
        @supports not (backdrop-filter: blur(2px)) {
            .liquid-nav,
            .control-container {
                background: linear-gradient(135deg, rgba(255, 255, 255, .08), rgba(255, 255, 255, .03));
            }
        }
    </style>
</head>
<body>

    <!-- ===== SVG 液态玻璃滤镜 ===== -->
    <svg class="liquid-filter">
        <filter id="liquid_glass_filter" x="-20%" y="-20%" width="140%" height="140%" filterUnits="objectBoundingBox">
            <feTurbulence type="fractalNoise" baseFrequency="0.01 0.01" numOctaves="1" seed="5" result="turbulence" />
            <feGaussianBlur in="turbulence" stdDeviation="3" result="softMap" />
            <feDisplacementMap in="SourceGraphic" in2="softMap" scale="100" xChannelSelector="R" yChannelSelector="G" />
        </filter>
    </svg>

    <!-- ===== 动态气泡背景 ===== -->
    <div class="bg-mesh">
        <div class="blob blob-1"></div>
        <div class="blob blob-2"></div>
        <div class="blob blob-3"></div>
    </div>

    <!-- ============================================
    🎛️ 灵动岛控制中心 (iOS风格)
    ============================================ -->
    <div class="control-island" id="control-island" role="button" tabindex="0" aria-expanded="false">
        <div class="control-container" id="control-container">
            <div class="control-compact" aria-hidden="false">
                <span class="ctrl-label"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:16px;height:16px;margin-right:4px;vertical-align:-2px;"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>控制中心</span>
                <span class="ctrl-hint">点击展开</span>
            </div>
            <div class="control-panel" aria-hidden="true" id="controlPanel">
                <!-- JS 动态渲染 -->
            </div>
        </div>
    </div>

    <!-- ============================================
    📦 主容器
    ============================================ -->
    <div class="container">

        <!-- ========== 仪表盘 ========== -->
        <section class="view active" id="view-dashboard">
            <div class="dashboard-row">
                <div class="stat-grid" id="statGrid"></div>
                <div class="glass-card chart-card">
                    <div class="chart-head">
                        <div class="chart-title"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>访问趋势</div>
                        <div class="chart-tabs" id="chartTabs">
                            <div class="chart-tab" data-d="7">7天</div>
                            <div class="chart-tab active" data-d="14">14天</div>
                            <div class="chart-tab" data-d="30">30天</div>
                        </div>
                    </div>
                    <canvas id="visitChart"></canvas>
                </div>
            </div>
            <div class="dashboard-row">
                <div class="glass-card">
                    <h3 class="section-title"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>站点信息</h3>
                    <div class="form-group"><label class="form-label">网站标题</label><input class="glass-input" id="siteTitle" placeholder="标题"></div>
                    <div class="form-group"><label class="form-label">简介</label><input class="glass-input" id="siteDesc" placeholder="简短描述"></div>
                    <div class="form-group">
                        <label class="form-label">图标</label>
                        <div class="img-upload">
                            <input class="glass-input" id="siteFavicon" placeholder="URL">
                            <button class="btn btn-secondary" data-target="siteFavicon" data-accept="image/*" data-preview="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="12" cy="12" r="3"/></svg> 上传</button>
                            <span class="img-preview-wrap"><img class="img-preview" id="siteFaviconPreview"><span class="del-img-btn" data-target="siteFavicon" data-preview="siteFaviconPreview" title="删除"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></span></span>
                        </div>
                    </div>
                    <button class="btn btn-primary btn-block" onclick="saveSiteInfo()">保存</button>
                </div>
                <div class="glass-card">
                    <h3 class="section-title"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>管理员账号</h3>
                    <div class="form-group"><label class="form-label">用户名</label><input class="glass-input" id="accUser" placeholder="管理员账号"></div>
                    <div class="form-group"><label class="form-label">原密码</label><input class="glass-input" id="accOld" type="password" placeholder="当前密码"></div>
                    <div class="form-group"><label class="form-label">新密码</label><input class="glass-input" id="accNew" type="password" placeholder="新密码"></div>
                    <div class="form-group"><label class="form-label">确认新密码</label><input class="glass-input" id="accRepass" type="password" placeholder="再次输入"></div>
                    <button class="btn btn-primary btn-block" onclick="saveAccount()">修改密码</button>
                </div>
            </div>
        </section>

        <!-- ========== 公告管理 (单条) ========== -->
        <section class="view" id="view-notices">
            <div class="manage-grid">
                <div class="glass-card">
                    <h3 class="section-title"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 5.882V19.24a1.76 1.76 0 0 1-3.417.592l-2.147-6.15M18 13a3 3 0 1 0 0-6M5.436 13.683A4.001 4.001 0 0 1 7 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 0 1-1.564-.317z"/></svg>公告管理</h3>
                    <div class="form-row">
                        <div class="form-group"><label class="form-label">标题</label><input class="glass-input" id="noticeTitle" placeholder="公告标题"></div>
                        <div class="form-group"><label class="form-label">跳转链接</label><input class="glass-input" id="noticeUrl" placeholder="https://..."></div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">内容</label>
                        <textarea class="glass-textarea" id="noticeContent" placeholder="公告正文..."></textarea>
                    </div>
                    <button class="btn btn-primary btn-block" onclick="saveNotice()">发布 / 更新公告</button>
                </div>
                <div class="glass-card">
                    <h3 class="section-title">当前公告</h3>
                    <div class="list-wrap">
                        <table class="data-list" id="noticesList"></table>
                    </div>
                </div>
            </div>
        </section>

        <!-- ========== 音乐管理 ========== -->
        <section class="view" id="view-music">
            <div class="manage-grid">
                <div class="glass-card">
                    <h3 class="section-title"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 18V5l12-2v13"/><circle cx="6" cy="18" r="3"/><circle cx="18" cy="16" r="3"/></svg>添加音乐</h3>
                    <div class="form-row">
                        <div class="form-group"><label class="form-label">歌曲</label><input class="glass-input" id="musicTitle" placeholder="歌曲名称"></div>
                        <div class="form-group"><label class="form-label">歌手</label><input class="glass-input" id="musicArtist" placeholder="歌手"></div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">音频地址</label>
                        <div class="img-upload">
                            <input class="glass-input" id="musicSrc" placeholder="URL">
                            <button class="btn btn-secondary" data-target="musicSrc" data-accept="audio/*" data-preview="false"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg> 上传</button>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">封面</label>
                        <div class="img-upload">
                            <input class="glass-input" id="musicCover" placeholder="URL">
                            <button class="btn btn-secondary" data-target="musicCover" data-accept="image/*" data-preview="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg> 上传</button>
                            <span class="img-preview-wrap"><img class="img-preview" id="musicCoverPreview"><span class="del-img-btn" data-target="musicCover" data-preview="musicCoverPreview" title="删除"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></span></span>
                        </div>
                    </div>
                    <button class="btn btn-primary btn-block" onclick="saveMusic()">添加音乐</button>
                </div>
                <div class="glass-card">
                    <h3 class="section-title">音乐列表</h3>
                    <div class="list-wrap">
                        <table class="data-list" id="musicList"></table>
                    </div>
                </div>
            </div>
        </section>

        <!-- ========== 头条管理 ========== -->
        <section class="view" id="view-news">
            <div class="manage-grid">
                <div class="glass-card">
                    <h3 class="section-title"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 22h16a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2H8a2 2 0 0 0-2 2v16a2 2 0 0 1-2 2Zm0 0a2 2 0 0 1-2-2v-9c0-1.1.9-2 2-2h2"/><path d="M18 14h-8"/><path d="M15 18h-5"/><path d="M10 6h8v4h-8V6Z"/></svg>添加新闻</h3>
                    <div class="form-row">
                        <div class="form-group"><label class="form-label">标题</label><input class="glass-input" id="newsTitle" placeholder="新闻标题"></div>
                        <div class="form-group"><label class="form-label">来源</label><input class="glass-input" id="newsSource" placeholder="来源"></div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">配图</label>
                        <div class="img-upload">
                            <input class="glass-input" id="newsImage" placeholder="URL">
                            <button class="btn btn-secondary" data-target="newsImage" data-accept="image/*" data-preview="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg> 上传</button>
                            <span class="img-preview-wrap"><img class="img-preview" id="newsImagePreview"><span class="del-img-btn" data-target="newsImage" data-preview="newsImagePreview" title="删除"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></span></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">链接</label>
                        <input class="glass-input" id="newsUrl" placeholder="点击跳转地址">
                    </div>
                    <button class="btn btn-primary btn-block" onclick="saveNews()">添加新闻</button>
                </div>
                <div class="glass-card">
                    <h3 class="section-title">新闻列表</h3>
                    <div class="list-wrap">
                        <table class="data-list" id="newsList"></table>
                    </div>
                </div>
            </div>
        </section>

        <!-- ========== 进度管理 ========== -->
        <section class="view" id="view-progress">
            <div class="manage-grid">
                <div class="glass-card">
                    <h3 class="section-title"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>添加进度</h3>
                    <div class="form-group"><label class="form-label">项目</label><input class="glass-input" id="progName" placeholder="项目名称"></div>
                    <div class="form-group"><label class="form-label">描述</label><input class="glass-input" id="progDesc" placeholder="描述"></div>
                    <div class="form-row">
                        <div class="form-group"><label class="form-label">状态</label>
                            <select class="glass-input" id="progStatus">
                                <option value="pending">待开发</option>
                                <option value="warning">调试中</option>
                                <option value="progress">开发中</option>
                                <option value="done">已完成</option>
                            </select>
                        </div>
                        <div class="form-group"><label class="form-label">进度 %</label>
                            <input class="glass-input" id="progPercent" type="number" min="0" max="100" value="0" style="width:70px;text-align:center;">
                        </div>
                    </div>
                    <button class="btn btn-primary btn-block" onclick="saveProgress()">添加进度</button>
                </div>
                <div class="glass-card">
                    <h3 class="section-title">进度列表</h3>
                    <div class="list-wrap">
                        <table class="data-list" id="progressList"></table>
                    </div>
                </div>
            </div>
        </section>

        <!-- ========== 卡片管理 ========== -->
        <section class="view" id="view-cards">
            <div class="glass-card" style="margin-bottom:12px">
                <div class="chart-head" style="margin-bottom:10px">
                    <h3 class="section-title" style="margin:0"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="M2 10h20"/></svg>导航卡片管理</h3>
                    <div class="chart-tabs" id="cardTabs">
                        <div class="chart-tab active" data-type="home">首页卡片</div>
                        <div class="chart-tab" data-type="recommend">推荐卡片</div>
                    </div>
                </div>
                <p style="font-size:11px;color:var(--text-muted);margin:0">首页卡片显示在主页，推荐卡片显示在推荐页面。支持添加多个卡片，用户可在前端下滑查看更多。</p>
            </div>
            <div class="manage-grid">
                <div class="glass-card">
                    <h3 class="section-title"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M12 8v8"/><path d="M8 12h8"/></svg>添加卡片</h3>
                    <div class="form-row">
                        <div class="form-group"><label class="form-label">网站名称</label><input class="glass-input" id="cardTitle" placeholder="网站名称"></div>
                        <div class="form-group"><label class="form-label">链接地址</label><input class="glass-input" id="cardUrl" placeholder="https://..."></div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">图标</label>
                        <div class="img-upload">
                            <input class="glass-input" id="cardIcon" placeholder="图标URL">
                            <button class="btn btn-secondary" data-target="cardIcon" data-accept="image/*" data-preview="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg> 上传</button>
                            <span class="img-preview-wrap"><img class="img-preview" id="cardIconPreview"><span class="del-img-btn" data-target="cardIcon" data-preview="cardIconPreview" title="删除"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></span></span>
                        </div>
                    </div>
                    <div class="form-group"><label class="form-label">简介</label><input class="glass-input" id="cardDesc" placeholder="简短描述"></div>
                    <div class="form-group">
                        <label class="form-label">标签（可选）</label>
                        <div style="display:flex;gap:8px;flex-wrap:wrap;margin-top:4px;align-items:center">
                            <label style="display:flex;align-items:center;gap:4px;font-size:11px;cursor:pointer"><input type="checkbox" id="tagHot" value="hot"> <span style="padding:2px 8px;border-radius:99px;background:rgba(255,69,58,0.15);color:#ff3b30">热门</span></label>
                            <label style="display:flex;align-items:center;gap:4px;font-size:11px;cursor:pointer"><input type="checkbox" id="tagTool" value="tool"> <span style="padding:2px 8px;border-radius:99px;background:rgba(0,122,255,0.15);color:#0a84ff">工具</span></label>
                            <label style="display:flex;align-items:center;gap:4px;font-size:11px;cursor:pointer"><input type="checkbox" id="tagBeauty" value="beauty"> <span style="padding:2px 8px;border-radius:99px;background:rgba(191,90,242,0.15);color:#bf5af2">设计</span></label>
                        </div>
                        <input class="glass-input" id="cardCustomTags" placeholder="自定义标签，用逗号分隔（如：搜索,社区,视频）" style="margin-top:8px;font-size:12px">
                    </div>
                    <button class="btn btn-primary btn-block" onclick="saveCard()">添加卡片</button>
                </div>
                <div class="glass-card">
                    <h3 class="section-title">卡片列表 <span id="cardCount" style="font-size:11px;color:var(--text-muted);font-weight:400"></span></h3>
                    <div class="list-wrap">
                        <table class="data-list" id="cardsList"></table>
                    </div>
                </div>
            </div>
        </section>

        <!-- ========== 反馈管理 ========== -->
        <section class="view" id="view-feedback">
            <div class="glass-card" style="margin-bottom:12px">
                <div class="chart-head" style="margin-bottom:10px">
                    <h3 class="section-title" style="margin:0"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>用户反馈管理 <span id="feedbackCount" style="font-size:11px;color:var(--text-muted);font-weight:400;margin-left:6px"></span></h3>
                </div>
                <p style="font-size:11px;color:var(--text-muted);margin:0">用户在「添加」页面提交的站点推荐和反馈内容会显示在这里。</p>
            </div>
            <div class="glass-card">
                <div class="list-wrap">
                    <table class="data-list" id="feedbackList"></table>
                </div>
            </div>
        </section>

        <!-- ========== 文件管理 ========== -->
        <section class="view" id="view-files">
            <div class="glass-card" style="margin-bottom:12px">
                <div class="chart-head" style="margin-bottom:10px">
                    <h3 class="section-title" style="margin:0"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/></svg>上传文件管理 <span id="fileMeta" style="font-size:11px;color:var(--text-muted);font-weight:400;margin-left:6px"></span></h3>
                </div>
                <p style="font-size:11px;color:var(--text-muted);margin:0">查看 uploads 文件夹中所有上传的图片/音频，支持删除无用文件释放空间。</p>
            </div>
            <div class="glass-card">
                <div style="display:flex;gap:10px;margin-bottom:12px;align-items:center;flex-wrap:wrap">
                    <div style="display:flex;gap:6px;align-items:center">
                        <label class="chip-label" style="display:inline-flex;align-items:center;gap:5px;padding:5px 12px;border-radius:99px;background:var(--glass-bg);border:1px solid var(--glass-border);font-size:11px;cursor:pointer"><input type="radio" name="fileFilter" value="all" checked> 全部</label>
                        <label class="chip-label" style="display:inline-flex;align-items:center;gap:5px;padding:5px 12px;border-radius:99px;background:var(--glass-bg);border:1px solid var(--glass-border);font-size:11px;cursor:pointer"><input type="radio" name="fileFilter" value="image"> 图片</label>
                        <label class="chip-label" style="display:inline-flex;align-items:center;gap:5px;padding:5px 12px;border-radius:99px;background:var(--glass-bg);border:1px solid var(--glass-border);font-size:11px;cursor:pointer"><input type="radio" name="fileFilter" value="audio"> 音频</label>
                    </div>
                    <button class="btn btn-sm btn-secondary" onclick="renderFiles()" style="margin-left:auto"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:13px;height:13px"><polyline points="23 4 23 10 17 10"/><path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"/></svg> 刷新</button>
                </div>
                <div class="file-grid" id="fileGrid" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(160px,1fr));gap:10px">
                </div>
            </div>
        </section>

    </div>

    <!-- ============================================
    🌟 液态玻璃底部导航
    ============================================ -->
    <nav class="liquid-nav" id="liquidNav">
        <div class="liquid-glare-container">
            <div class="liquid-glare" id="navGlare"></div>
        </div>
        <div class="nav-items">
            <div class="active-pill" id="activePill"></div>
            <button class="nav-btn active" data-view="dashboard" data-label="仪表盘">
                <div class="btn-content">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>
                    <span>仪表</span>
                </div>
            </button>
            <button class="nav-btn" data-view="notices" data-label="公告">
                <div class="btn-content">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 5.882V19.24a1.76 1.76 0 0 1-3.417.592l-2.147-6.15M18 13a3 3 0 1 0 0-6M5.436 13.683A4.001 4.001 0 0 1 7 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 0 1-1.564-.317z"/></svg>
                    <span>公告</span>
                </div>
            </button>
            <button class="nav-btn" data-view="news" data-label="头条">
                <div class="btn-content">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 22h16a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2H8a2 2 0 0 0-2 2v16a2 2 0 0 1-2 2Zm0 0a2 2 0 0 1-2-2v-9c0-1.1.9-2 2-2h2"/><path d="M18 14h-8"/><path d="M15 18h-5"/><path d="M10 6h8v4h-8V6Z"/></svg>
                    <span>头条</span>
                </div>
            </button>
            <button class="nav-btn" data-view="cards" data-label="卡片">
                <div class="btn-content">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="M2 10h20"/></svg>
                    <span>卡片</span>
                </div>
            </button>
            <button class="nav-btn" data-view="music" data-label="音乐">
                <div class="btn-content">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 18V5l12-2v13"/><circle cx="6" cy="18" r="3"/><circle cx="18" cy="16" r="3"/></svg>
                    <span>音乐</span>
                </div>
            </button>
            <button class="nav-btn" data-view="progress" data-label="进度">
                <div class="btn-content">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>
                    <span>进度</span>
                </div>
            </button>
            <button class="nav-btn" data-view="feedback" data-label="反馈">
                <div class="btn-content">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                    <span>反馈</span>
                </div>
            </button>
            <button class="nav-btn" data-view="files" data-label="文件">
                <div class="btn-content">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/></svg>
                    <span>文件</span>
                </div>
            </button>
        </div>
        <div class="nav-divider"></div>
        <button class="theme-btn" id="themeBtn" aria-label="切换主题">
            <div class="theme-icon-wrapper">
                <svg class="sun" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="5"/><line x1="12" y1="1" x2="12" y2="3"/><line x1="12" y1="21" x2="12" y2="23"/><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/><line x1="1" y1="12" x2="3" y2="12"/><line x1="21" y1="12" x2="23" y2="12"/><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/>
                </svg>
                <svg class="moon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/>
                </svg>
            </div>
        </button>
    </nav>

    <!-- ===== Toast ===== -->
    <div class="toast" id="toast"></div>

    <!-- ===== 模态框 ===== -->
    <div class="modal-overlay" id="modal">
        <div class="glass-card modal">
            <h3 id="modalTitle">编辑</h3>
            <div id="modalBody"></div>
            <div class="modal-actions">
                <button class="btn btn-secondary" onclick="closeModal()">取消</button>
                <button class="btn btn-primary" id="modalOk">确定</button>
            </div>
        </div>
    </div>

    <!-- ============================================
    📦 API & 主逻辑
    ============================================ -->
    <script src="api/settings.js">
    </script>
    <script>
        /* =========================================================
           主逻辑 - 兼容 settings.js
           ========================================================= */
        if (typeof NavSettings === 'undefined') {
            var NavSettings = {
                getAll: function() { try { return JSON.parse(localStorage.getItem('nav_admin_settings') ||
                            '{}'); } catch (e) { return {}; } },
                get: function(k) { var d = this.getAll(); return d[k]; },
                set: function(k, v) { var d = this.getAll();
                    d[k] = v;
                    localStorage.setItem('nav_admin_settings', JSON.stringify(d)); return d; },
                setAll: function(o) { var d = this.getAll();
                    Object.assign(d, o);
                    localStorage.setItem('nav_admin_settings', JSON.stringify(d)); return d; },
                uid: function() { return Date.now().toString(36) + Math.random().toString(36).slice(2, 6); },
                recordVisit: function() { return 0; },
                getTrend: function(days) { var arr = [],
                        d = new Date(); for (var i = days - 1; i >= 0; i--) { var dd = new Date(d);
                        dd.setDate(d.getDate() - i);
                        arr.push({ date: dd.toLocaleDateString('zh-CN'), count: 0 }); } return arr; },
                dataUrl: function(file) { return new Promise(function(resolve, reject) { var r = new FileReader();
                        r.onload = function() { resolve(r.result); };
                        r.onerror = reject;
                        r.readAsDataURL(file); }); }
            };
        }

        /* ---------- Toast ---------- */
        function toast(msg) {
            var t = document.getElementById('toast');
            if (!t) return;
            t.textContent = msg;
            t.classList.add('show');
            clearTimeout(t._timer);
            t._timer = setTimeout(function() { t.classList.remove('show'); }, 1800);
        }

        /* ---------- 导航 ---------- */
        var navBtns = document.querySelectorAll('.nav-btn');
        var activePill = document.getElementById('activePill');
        var navEl = document.getElementById('liquidNav');
        var glareEl = document.getElementById('navGlare');

        function updatePill(btn, smooth) {
            if (!btn || !activePill) return;
            var parent = btn.closest('.nav-items');
            if (!parent) return;
            var rect = btn.getBoundingClientRect();
            var parentRect = parent.getBoundingClientRect();
            if (!smooth) { activePill.style.transition = 'none'; } else { activePill.style.transition =
                    'transform 0.45s cubic-bezier(0.34, 1.2, 0.64, 1), width 0.45s cubic-bezier(0.34, 1.2, 0.64, 1), background 0.45s ease, box-shadow 0.45s ease'; }
            activePill.style.width = rect.width + 'px';
            activePill.style.transform = 'translateX(' + (rect.left - parentRect.left) + 'px)';
        }

        function switchView(view) {
            document.querySelectorAll('.view').forEach(function(v) { v.classList.toggle('active', v.id === 'view-' +
                    view); });
            navBtns.forEach(function(b) { b.classList.toggle('active', b.dataset.view === view); });
            var active = document.querySelector('.nav-btn[data-view="' + view + '"]');
            if (active) updatePill(active, true);
            if (view === 'dashboard') renderDashboard();
            if (view === 'dashboard') renderSettings();
            if (view === 'notices') renderNotices();
            if (view === 'music') renderMusic();
            if (view === 'news') renderNews();
            if (view === 'progress') renderProgress();
            if (view === 'cards') { _refreshCardListRefs(); renderCards(); }
            if (view === 'feedback') renderFeedback();
            if (view === 'files') renderFiles();
        }

        navBtns.forEach(function(b) { b.addEventListener('click', function() { switchView(this.dataset.view); }); });

        window.addEventListener('resize', function() {
            var active = document.querySelector('.nav-btn.active');
            if (active) updatePill(active, false);
        });

        if (navEl && glareEl) {
            navEl.addEventListener('mousemove', function(e) {
                var rect = navEl.getBoundingClientRect();
                var x = e.clientX - rect.left;
                var y = e.clientY - rect.top;
                glareEl.style.setProperty('--x', x + 'px');
                glareEl.style.setProperty('--y', y + 'px');
            });
        }

        /* ---------- 主题 ---------- */
        var themeBtn = document.getElementById('themeBtn');
        if (themeBtn) {
            themeBtn.addEventListener('click', function() {
                var root = document.documentElement;
                var isDark = root.getAttribute('data-theme') === 'dark';
                root.setAttribute('data-theme', isDark ? 'light' : 'dark');
                // 同步主题开关 (如果有)
                var themeSwitch = document.querySelector('#controlPanel .ctrl-item[data-key="theme"]');
                if (themeSwitch) themeSwitch.classList.toggle('active', !isDark);
                setTimeout(function() {
                    var active = document.querySelector('.nav-btn.active');
                    if (active) updatePill(active, true);
                }, 100);
            });
        }

        /* =========================================================
           🔐 登录验证
           ========================================================= */
        function checkAuth() {
            return fetch('api.php?action=check_login', { credentials: 'include' })
                .then(function (res) { return res.json(); })
                .then(function (data) {
                    if (data.code !== 0) {
                        localStorage.removeItem('nav_admin_logged_in');
                        window.location.href = 'login.php';
                        return false;
                    }
                    return true;
                })
                .catch(function () {
                    // 离线状态：检查本地标记
                    if (localStorage.getItem('nav_admin_logged_in') !== '1') {
                        window.location.href = 'login.php';
                        return false;
                    }
                    return true;
                });
        }

        function logout() {
            fetch('api.php?action=logout', { credentials: 'include' })
                .catch(function () { })
                .finally(function () {
                    localStorage.removeItem('nav_admin_logged_in');
                    window.location.href = 'login.php';
                });
        }

        /* =========================================================
           仪表盘
           ========================================================= */
        var STAT_ICONS = {
            visit: '<svg viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2z"/><path d="M12 6v6l4 2"/></svg>',
            notice: '<svg viewBox="0 0 24 24"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>',
            card: '<svg viewBox="0 0 24 24"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="M2 10h20"/></svg>',
            recommend: '<svg viewBox="0 0 24 24"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>',
            feedback: '<svg viewBox="0 0 24 24"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>',
            music: '<svg viewBox="0 0 24 24"><path d="M9 18V5l12-2v13"/><circle cx="6" cy="18" r="3"/><circle cx="18" cy="16" r="3"/></svg>',
            news: '<svg viewBox="0 0 24 24"><path d="M4 22h16a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2H8a2 2 0 0 0-2 2v16a2 2 0 0 1-2 2Zm0 0a2 2 0 0 1-2-2v-9c0-1.1.9-2 2-2h2"/><path d="M18 14h-8"/><path d="M15 18h-5"/><path d="M10 6h8v4h-8V6Z"/></svg>',
            toggle: '<svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>'
        };

        function renderDashboard() {
            var s = NavSettings.getAll();
            var recTotal = (s.recommendCards || []).length;
            // 兼容旧 recommendBatches（统计拍平，避免老数据遗漏）
            if (!recTotal && s.recommendBatches) {
                recTotal = s.recommendBatches.reduce(function(n, b) { return n + (Array.isArray(b) ? b.length : 0); }, 0);
            }
            // 已开启功能数：基于 settings 读取（不是 s 本身，避免 undefined）
            var _ns = NavSettings.get('settings') || s;
            var onCount = [
                _ns.showMusicList !== false ? 1 : 0,
                _ns.showProgressPanel !== false ? 1 : 0,
                _ns.showNews !== false ? 1 : 0,
                _ns.showSearchTime !== false ? 1 : 0,
                _ns.showDailyQuote !== false ? 1 : 0
            ].filter(Boolean).length;
            var stats = [
                { icon: STAT_ICONS.visit, color: '#1e9fff', num: s.visitCount || 0, label: '访问量' },
                { icon: STAT_ICONS.notice, color: '#ff5722', num: (s.notices || []).length, label: '公告' },
                { icon: STAT_ICONS.card, color: '#ffb800', num: (s.homeCards || []).length, label: '首页卡片' },
                { icon: STAT_ICONS.recommend, color: '#8b5cf6', num: recTotal, label: '推荐卡片' },
                { icon: STAT_ICONS.feedback, color: '#16baaa', num: (s.feedback || []).length, label: '反馈' },
                { icon: STAT_ICONS.music, color: '#9c27b0', num: (s.musicPlaylist || []).length, label: '音乐' },
                { icon: STAT_ICONS.news, color: '#2f4056', num: (s.newsItems || []).length, label: '头条' },
                { icon: STAT_ICONS.toggle, color: '#01aaed', num: onCount + '/5', label: '已开启' }
            ];
            document.getElementById('statGrid').innerHTML = stats.map(function(it) {
                return '<div class="glass-card stat-card">' +
                    '<div class="stat-icon" style="color:' + it.color + '">' + it.icon + '</div>' +
                    '<div class="stat-num" style="color:' + it.color + '">' + it.num + '</div>' +
                    '<div class="stat-label">' + it.label + '</div></div>';
            }).join('');
            var activeTab = document.querySelector('#chartTabs .active');
            var days = activeTab ? parseInt(activeTab.dataset.d) : 14;
            drawChart(days);
        }

        var _drawRetry = 0;
        function drawChart(days) {
            var canvas = document.getElementById('visitChart');
            if (!canvas) return;
            var ctx = canvas.getContext('2d');
            var dpr = window.devicePixelRatio || 1;
            var w = canvas.clientWidth,
                h = canvas.clientHeight;
            if (w === 0 || h === 0) {
                // 画布还没布局好，最多重试 5 次防止卡死
                if (_drawRetry < 5) {
                    _drawRetry++;
                    setTimeout(function() { drawChart(days); }, 150);
                } else {
                    _drawRetry = 0; // 重置供下次使用
                }
                return;
            }
            _drawRetry = 0; // 成功后重置
            canvas.width = w * dpr;
            canvas.height = h * dpr;
            ctx.scale(dpr, dpr);
            ctx.clearRect(0, 0, w, h);

            var data = NavSettings.getTrend(days);
            var total = data.reduce(function(a, b) { return a + b.count; }, 0);
            if (total === 0) {
                ctx.fillStyle = '#94a3b8';
                ctx.font = '12px system-ui';
                ctx.textAlign = 'center';
                ctx.fillText('暂无数据', w / 2, h / 2);
                return;
            }

            var pad = { l: 32, r: 12, t: 12, b: 22 };
            var cw = w - pad.l - pad.r,
                ch = h - pad.t - pad.b;
            var max = Math.max.apply(null, data.map(function(d) { return d.count; }));
            max = Math.max(max, 1);
            var niceMax = Math.ceil(max / 5) * 5;
            var xStep = data.length > 1 ? cw / (data.length - 1) : 0;

            ctx.strokeStyle = 'rgba(0,0,0,0.05)';
            ctx.lineWidth = 1;
            for (var i = 0; i <= 4; i++) {
                var y = pad.t + ch * i / 4;
                ctx.beginPath();
                ctx.moveTo(pad.l, y);
                ctx.lineTo(w - pad.r, y);
                ctx.stroke();
                ctx.fillStyle = '#94a3b8';
                ctx.font = '9px system-ui';
                ctx.textAlign = 'right';
                ctx.fillText(niceMax - niceMax * i / 4, pad.l - 4, y + 3);
            }

            ctx.fillStyle = '#94a3b8';
            ctx.font = '9px system-ui';
            ctx.textAlign = 'center';
            data.forEach(function(d, idx) {
                var x = pad.l + idx * xStep;
                var label = d.date.split('/').slice(-2).join('/');
                ctx.fillText(label, x, h - 6);
            });

            var grad = ctx.createLinearGradient(0, pad.t, 0, h - pad.b);
            grad.addColorStop(0, 'rgba(59,130,246,0.30)');
            grad.addColorStop(1, 'rgba(59,130,246,0.02)');
            var points = data.map(function(d, idx) {
                return [pad.l + idx * xStep, pad.t + ch * (1 - d.count / niceMax)];
            });

            ctx.beginPath();
            ctx.moveTo(points[0][0], h - pad.b);
            points.forEach(function(p) { ctx.lineTo(p[0], p[1]); });
            ctx.lineTo(points[points.length - 1][0], h - pad.b);
            ctx.closePath();
            ctx.fillStyle = grad;
            ctx.fill();

            ctx.beginPath();
            points.forEach(function(p, idx) {
                if (idx === 0) ctx.moveTo(p[0], p[1]);
                else ctx.lineTo(p[0], p[1]);
            });
            ctx.strokeStyle = '#3b82f6';
            ctx.lineWidth = 2;
            ctx.stroke();

            points.forEach(function(p) {
                ctx.beginPath();
                ctx.arc(p[0], p[1], 3, 0, Math.PI * 2);
                ctx.fillStyle = '#fff';
                ctx.fill();
                ctx.strokeStyle = '#3b82f6';
                ctx.lineWidth = 1.5;
                ctx.stroke();
            });
        }

        document.addEventListener('click', function(e) {
            var tab = e.target.closest('#chartTabs .chart-tab');
            if (tab) {
                document.querySelectorAll('#chartTabs .chart-tab').forEach(function(t) { t.classList.remove(
                    'active'); });
                tab.classList.add('active');
                drawChart(parseInt(tab.dataset.d));
            }
        });

        /* =========================================================
           🔔 公告管理 - 单条模式
           ========================================================= */
        function saveNotice() {
            var title = document.getElementById('noticeTitle').value.trim();
            var content = document.getElementById('noticeContent').value.trim();
            var url = document.getElementById('noticeUrl').value.trim();
            if (!title) { toast('请输入标题'); return; }
            // 只保留一条公告
            var list = [{ id: NavSettings.uid(), title: title, content: content, url: url, date: new Date()
                    .toLocaleDateString('zh-CN') }];
            NavSettings.set('notices', list);
            document.getElementById('noticeTitle').value = '';
            document.getElementById('noticeContent').value = '';
            document.getElementById('noticeUrl').value = '';
            toast('公告已发布');
            renderNotices();
        }

        function renderNotices() {
            var list = NavSettings.get('notices') || [];
            var el = document.getElementById('noticesList');
            if (!list.length) {
                el.innerHTML = '<tr><td colspan="4" style="text-align:center;padding:16px;color:#94a3b8">暂无公告</td></tr>';
                return;
            }
            // 只显示第一条
            var n = list[0];
            var viewBtn = n.url ? '<button class="btn btn-sm btn-success" onclick="window.open(\'' + n.url +
                '\',\'_blank\')">查看</button>' :
                '<button class="btn btn-sm btn-secondary" disabled style="opacity:0.5">查看</button>';
            el.innerHTML = '<thead><tr><th>标题</th><th>内容</th><th>日期</th><th>操作</th></tr></thead><tbody><tr>' +
                '<td><b>' + n.title + '</b></td>' +
                '<td style="max-width:120px;overflow:hidden;text-overflow:ellipsis">' + ((n.content || '').slice(0,
                    30)) + ((n.content && n.content.length > 30) ? '…' : '') + '</td>' +
                '<td>' + (n.date || '') + '</td>' +
                '<td class="col-actions">' +
                viewBtn +
                '<button class="btn btn-sm btn-secondary" onclick="editNotice(0)">编辑</button>' +
                '<button class="btn btn-sm btn-danger" onclick="delNotice(0)">删除</button>' +
                '</td></tr></tbody>';
        }

        function editNotice(i) {
            var list = NavSettings.get('notices') || [];
            var n = list[i];
            if (!n) return;
            openModal('编辑公告',
                '<div class="form-group"><label class="form-label">标题</label><input class="glass-input" id="mTitle" value="' +
                n.title + '"></div>' +
                '<div class="form-group"><label class="form-label">内容</label><textarea class="glass-textarea" id="mContent">' +
                (n.content || '') + '</textarea></div>' +
                '<div class="form-group"><label class="form-label">跳转链接</label><input class="glass-input" id="mUrl" value="' +
                (n.url || '') + '"></div>',
                function() {
                    n.title = document.getElementById('mTitle').value.trim();
                    n.content = document.getElementById('mContent').value;
                    n.url = document.getElementById('mUrl').value.trim();
                    list[i] = n;
                    NavSettings.set('notices', list);
                    renderNotices();
                    closeModal();
                    toast('已更新');
                });
        }

        function delNotice(i) {
            if (!confirm('确定删除这条公告？')) return;
            var list = [];
            NavSettings.set('notices', list);
            renderNotices();
            toast('已删除');
        }

        /* =========================================================
           🎵 音乐管理
           ========================================================= */
        function saveMusic() {
            var item = {
                id: NavSettings.uid(),
                title: document.getElementById('musicTitle').value.trim(),
                artist: document.getElementById('musicArtist').value.trim(),
                src: document.getElementById('musicSrc').value.trim(),
                cover: document.getElementById('musicCover').value.trim()
            };
            if (!item.title) { toast('请输入歌曲名称'); return; }
            if (!item.src) { toast('请输入音频地址'); return; }
            var list = NavSettings.get('musicPlaylist') || [];
            list.push(item);
            NavSettings.set('musicPlaylist', list);
            ['musicTitle', 'musicArtist', 'musicSrc', 'musicCover'].forEach(function(id) { document.getElementById(id)
                    .value = ''; });
            clearPreview('musicCoverPreview');
            toast('音乐已添加');
            renderMusic();
        }

        function renderMusic() {
            var list = NavSettings.get('musicPlaylist') || [];
            var el = document.getElementById('musicList');
            if (!list.length) {
                el.innerHTML = '<tr><td colspan="4" class="empty-state">暂无音乐</td></tr>';
                return;
            }
            el.innerHTML = '<thead><tr><th>封面</th><th>歌曲</th><th>歌手</th><th>操作</th></tr></thead><tbody>' +
                list.map(function(m, i) {
                    var coverHtml = m.cover ? '<img class="thumb" src="' + m.cover +
                        '" onerror="this.style.display=\'none\'">' : '-';
                    return '<tr><td>' + coverHtml + '</td><td><b>' + m.title + '</b></td><td>' + (m.artist ||
                        '') + '</td>' +
                        '<td class="col-actions">' +
                        '<button class="btn btn-sm btn-secondary" onclick="editMusic(' + i +
                        ')">编辑</button>' +
                        '<button class="btn btn-sm btn-danger" onclick="delMusic(' + i +
                        ')">删除</button></td></tr>';
                }).join('') + '</tbody>';
        }

        function editMusic(i) {
            var list = NavSettings.get('musicPlaylist') || [];
            var m = list[i];
            if (!m) return;
            openModal('编辑音乐',
                '<div class="form-group"><label class="form-label">歌曲</label><input class="glass-input" id="mTitle" value="' +
                m.title + '"></div>' +
                '<div class="form-group"><label class="form-label">歌手</label><input class="glass-input" id="mArtist" value="' +
                (m.artist || '') + '"></div>' +
                '<div class="form-group"><label class="form-label">音频URL</label><input class="glass-input" id="mSrc" value="' +
                (m.src || '') + '"></div>' +
                '<div class="form-group"><label class="form-label">封面URL</label><input class="glass-input" id="mCover" value="' +
                (m.cover || '') + '"></div>',
                function() {
                    m.title = document.getElementById('mTitle').value.trim();
                    m.artist = document.getElementById('mArtist').value.trim();
                    m.src = document.getElementById('mSrc').value.trim();
                    m.cover = document.getElementById('mCover').value.trim();
                    list[i] = m;
                    NavSettings.set('musicPlaylist', list);
                    renderMusic();
                    closeModal();
                    toast('已更新');
                });
        }

        function delMusic(i) {
            if (!confirm('确定删除？')) return;
            var list = NavSettings.get('musicPlaylist') || [];
            list.splice(i, 1);
            NavSettings.set('musicPlaylist', list);
            renderMusic();
            toast('已删除');
        }

        /* =========================================================
           📰 头条管理
           ========================================================= */
        function saveNews() {
            var item = {
                id: NavSettings.uid(),
                title: document.getElementById('newsTitle').value.trim(),
                source: document.getElementById('newsSource').value.trim(),
                image: document.getElementById('newsImage').value.trim(),
                url: document.getElementById('newsUrl').value.trim()
            };
            if (!item.title) { toast('请输入标题'); return; }
            var list = NavSettings.get('newsItems') || [];
            list.push(item);
            NavSettings.set('newsItems', list);
            ['newsTitle', 'newsSource', 'newsImage', 'newsUrl'].forEach(function(id) { document.getElementById(id)
                    .value = ''; });
            clearPreview('newsImagePreview');
            toast('新闻已添加');
            renderNews();
        }

        function renderNews() {
            var list = NavSettings.get('newsItems') || [];
            var el = document.getElementById('newsList');
            if (!list.length) {
                el.innerHTML = '<tr><td colspan="4" class="empty-state">暂无新闻</td></tr>';
                return;
            }
            el.innerHTML = '<thead><tr><th>配图</th><th>标题</th><th>来源</th><th>操作</th></tr></thead><tbody>' +
                list.map(function(n, i) {
                    var imgHtml = n.image ? '<img class="thumb" style="width:24px;height:18px" src="' + n
                        .image + '" onerror="this.style.display=\'none\'">' : '-';
                    return '<tr><td>' + imgHtml + '</td><td style="max-width:100px;overflow:hidden;text-overflow:ellipsis"><b>' +
                        n.title + '</b></td><td>' + (n.source || '') + '</td>' +
                        '<td class="col-actions">' +
                        '<button class="btn btn-sm btn-secondary" onclick="editNews(' + i +
                        ')">编辑</button>' +
                        '<button class="btn btn-sm btn-danger" onclick="delNews(' + i +
                        ')">删除</button></td></tr>';
                }).join('') + '</tbody>';
        }

        function editNews(i) {
            var list = NavSettings.get('newsItems') || [];
            var n = list[i];
            if (!n) return;
            openModal('编辑新闻',
                '<div class="form-group"><label class="form-label">标题</label><input class="glass-input" id="mTitle" value="' +
                n.title + '"></div>' +
                '<div class="form-group"><label class="form-label">来源</label><input class="glass-input" id="mSource" value="' +
                (n.source || '') + '"></div>' +
                '<div class="form-group"><label class="form-label">配图URL</label><input class="glass-input" id="mImage" value="' +
                (n.image || '') + '"></div>' +
                '<div class="form-group"><label class="form-label">链接</label><input class="glass-input" id="mUrl" value="' +
                (n.url || '') + '"></div>',
                function() {
                    n.title = document.getElementById('mTitle').value.trim();
                    n.source = document.getElementById('mSource').value.trim();
                    n.image = document.getElementById('mImage').value.trim();
                    n.url = document.getElementById('mUrl').value.trim();
                    list[i] = n;
                    NavSettings.set('newsItems', list);
                    renderNews();
                    closeModal();
                    toast('已更新');
                });
        }

        function delNews(i) {
            if (!confirm('确定删除？')) return;
            var list = NavSettings.get('newsItems') || [];
            list.splice(i, 1);
            NavSettings.set('newsItems', list);
            renderNews();
            toast('已删除');
        }

        /* =========================================================
           📈 进度管理
           ========================================================= */
        var STATUS_MAP = {
            done: { label: '已完成', cls: 'status-done', fill: 'done' },
            progress: { label: '开发中', cls: 'status-progress', fill: 'progress' },
            warning: { label: '调试中', cls: 'status-warning', fill: 'warning' },
            pending: { label: '待开发', cls: 'status-pending', fill: 'pending' }
        };

        function saveProgress() {
            var name = document.getElementById('progName').value.trim();
            if (!name) { toast('请输入项目名称'); return; }
            var desc = document.getElementById('progDesc').value.trim();
            var status = document.getElementById('progStatus').value;
            var percent = Math.max(0, Math.min(100, parseInt(document.getElementById('progPercent').value) || 0));
            var item = { id: NavSettings.uid(), name: name, desc: desc, status: status, percent: percent };
            var list = NavSettings.get('progressItems') || [];
            list.push(item);
            NavSettings.set('progressItems', list);
            ['progName', 'progDesc'].forEach(function(id) { document.getElementById(id).value = ''; });
            document.getElementById('progPercent').value = 0;
            toast('已添加');
            renderProgress();
        }

        function renderProgress() {
            var list = NavSettings.get('progressItems') || [];
            var el = document.getElementById('progressList');
            if (!list.length) {
                el.innerHTML = '<tr><td colspan="4" class="empty-state">暂无进度</td></tr>';
                return;
            }
            el.innerHTML = '<thead><tr><th>项目</th><th>进度</th><th>状态</th><th>操作</th></tr></thead><tbody>' +
                list.map(function(p, i) {
                    var s = STATUS_MAP[p.status] || STATUS_MAP.pending;
                    return '<tr><td><b>' + p.name + '</b><br><small style="color:var(--text-muted)">' + (p
                        .desc || '') + '</small></td>' +
                        '<td style="min-width:80px"><div class="progress-bar"><div class="progress-fill ' +
                        s.fill + '" style="width:' + p.percent + '%"></div></div><small style="color:var(--text-muted)">' +
                        p.percent + '%</small></td>' +
                        '<td><span class="status-badge ' + s.cls + '">' + s.label + '</span></td>' +
                        '<td class="col-actions">' +
                        '<button class="btn btn-sm btn-secondary" onclick="editProgress(' + i +
                        ')">编辑</button>' +
                        '<button class="btn btn-sm btn-danger" onclick="delProgress(' + i +
                        ')">删除</button></td></tr>';
                }).join('') + '</tbody>';
        }

        function editProgress(i) {
            var list = NavSettings.get('progressItems') || [];
            var p = list[i];
            if (!p) return;
            openModal('编辑进度',
                '<div class="form-group"><label class="form-label">项目</label><input class="glass-input" id="mName" value="' +
                p.name + '"></div>' +
                '<div class="form-group"><label class="form-label">描述</label><input class="glass-input" id="mDesc" value="' +
                (p.desc || '') + '"></div>' +
                '<div class="form-row"><div class="form-group"><label class="form-label">状态</label><select class="glass-input" id="mStatus">' +
                '<option value="pending"' + (p.status === 'pending' ? ' selected' : '') + '>待开发</option>' +
                '<option value="warning"' + (p.status === 'warning' ? ' selected' : '') + '>调试中</option>' +
                '<option value="progress"' + (p.status === 'progress' ? ' selected' : '') + '>开发中</option>' +
                '<option value="done"' + (p.status === 'done' ? ' selected' : '') + '>已完成</option></select></div>' +
                '<div class="form-group"><label class="form-label">进度 %</label><input class="glass-input" id="mPercent" type="number" min="0" max="100" value="' +
                p.percent + '"></div></div>',
                function() {
                    p.name = document.getElementById('mName').value.trim();
                    p.desc = document.getElementById('mDesc').value.trim();
                    p.status = document.getElementById('mStatus').value;
                    p.percent = Math.max(0, Math.min(100, parseInt(document.getElementById('mPercent').value) ||
                        0));
                    if (p.status === 'done') p.percent = 100;
                    list[i] = p;
                    NavSettings.set('progressItems', list);
                    renderProgress();
                    closeModal();
                    toast('已更新');
                });
        }

        function delProgress(i) {
            if (!confirm('确定删除？')) return;
            var list = NavSettings.get('progressItems') || [];
            var target = list[i];
            var targetId = target ? target.id : '';
            list.splice(i, 1);
            NavSettings.set('progressItems', list);
            // 同步后端
            fetch('api.php?action=delete_progress', {
                method: 'POST', headers: { 'Content-Type': 'application/json' },
                credentials: 'include', body: JSON.stringify({ id: targetId })
            }).catch(function() {});
            renderProgress();
            toast('已删除');
        }

        /* =========================================================
           💬 反馈管理 - 渲染用户提交的反馈内容
           ========================================================= */
        // 反馈列表全局缓存（供 viewFeedback 按 id 查找）
        var _feedbackListCache = [];

        function renderFeedback() {
            fetch('api.php?action=get_feedback', { credentials: 'include' })
                .then(function(res) { return res.json(); })
                .then(function(res) {
                    var list = (res.code === 0 && res.data) ? res.data : (NavSettings.get('feedback') || []);
                    _feedbackListCache = Array.isArray(list) ? list.slice() : [];
                    _renderFeedbackTable(list, false);
                }).catch(function() {
                    var list = NavSettings.get('feedback') || [];
                    _feedbackListCache = Array.isArray(list) ? list.slice() : [];
                    _renderFeedbackTable(list, true);
                });
        }

        function _renderFeedbackTable(list, isLocal) {
            var el = document.getElementById('feedbackList');
            var cntEl = document.getElementById('feedbackCount');
            if (cntEl) cntEl.textContent = '（共 ' + list.length + ' 条' + (isLocal ? ' · 本地缓存）' : '）');
            if (!list.length) {
                el.innerHTML = '<tr><td colspan="4" class="empty-state">暂无反馈内容' + (isLocal ? '（API不可用）' : '') + '</td></tr>';
                return;
            }
            // 按时间倒序
            var sorted = list.slice().sort(function(a, b) {
                return (b.time || '').localeCompare(a.time || '');
            });
            el.innerHTML = '<thead><tr><th style="width:55px">#</th><th style="min-width:300px;max-width:500px">内容（点击查看全文）</th><th style="width:160px">时间</th><th style="width:90px">操作</th></tr></thead><tbody>' +
                sorted.map(function(f, i) {
                    var rawText = f.content || '';
                    // 预览截断到 60 字
                    var preview = rawText.length > 60 ? rawText.slice(0, 60) + '...' : rawText;
                    var escaped = preview.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/\n/g, '<br>');
                    return '<tr><td style="text-align:center;color:var(--text-muted);font-size:12px">' + (i + 1) + '</td>' +
                        '<td style="line-height:1.6;word-break:break-word;max-width:500px;cursor:pointer;color:var(--text)" onclick="viewFeedback(\'' + f.id + '\')" title="点击查看全文">' + escaped + ' <span style="color:var(--primary);font-size:11px;white-space:nowrap">[查看]</span></td>' +
                        '<td style="font-size:12px;color:var(--text-muted);white-space:nowrap">' + (f.time || '-') + '</td>' +
                        '<td class="col-actions">' +
                        '<button class="btn btn-sm btn-danger" onclick="delFeedback(\'' + f.id + '\')">删除</button></td></tr>';
                }).join('') + '</tbody>';
        }

        function delFeedback(id) {
            if (!confirm('确定删除这条反馈？')) return;
            var list = NavSettings.get('feedback') || [];
            var newList = list.filter(function(f) { return f.id !== id; });
            NavSettings.set('feedback', newList);
            fetch('api.php?action=delete_feedback', {
                method: 'POST', headers: { 'Content-Type': 'application/json' },
                credentials: 'include', body: JSON.stringify({ id: id })
            }).catch(function() {});
            renderFeedback();
            toast('已删除');
        }

        // 弹窗查看反馈全文
        function viewFeedback(id) {
            var f = null;
            for (var i = 0; i < _feedbackListCache.length; i++) {
                if (_feedbackListCache[i].id === id) { f = _feedbackListCache[i]; break; }
            }
            if (!f) { toast('反馈内容不存在'); return; }
            var content = f.content || '';
            var time = f.time || '';
            var ip = f.ip || '';
            var escaped = content.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/\n/g, '<br>');
            var metaHtml = '<div style="font-size:12px;color:var(--text-muted);margin-bottom:12px;padding-bottom:8px;border-bottom:1px solid var(--border);display:flex;gap:16px;flex-wrap:wrap">';
            if (time) metaHtml += '<span>🕒 ' + time + '</span>';
            if (ip) metaHtml += '<span>📍 ' + ip + '</span>';
            metaHtml += '</div>';
            var body = metaHtml + '<div style="line-height:1.8;word-break:break-word;max-height:60vh;overflow-y:auto;padding-right:4px">' + escaped + '</div>';
            openModal('反馈详情', body, closeModal, { okText: '关闭', onlyOk: true });
        }

        /* =========================================================
           📁 文件管理 - 管理 uploads 文件夹下所有上传的文件
           ========================================================= */
        var _fileCache = [];
        function renderFiles() {
            var grid = document.getElementById('fileGrid');
            if (!grid) return;
            grid.innerHTML = '<div style="grid-column:1/-1;text-align:center;padding:30px;color:var(--text-muted);opacity:0.7"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:20px;height:20px;display:inline-block;vertical-align:middle;margin-right:8px;animation:spin 1s linear infinite"><path d="M21 12a9 9 0 1 1-6.219-8.56"/></svg>加载中...</div>';
            fetch('api.php?action=list_files', { credentials: 'include' })
                .then(function(res) { return res.json(); })
                .then(function(res) {
                    if (res.code !== 0) { throw new Error(res.msg || '加载失败'); }
                    _fileCache = res.data || [];
                    var metaEl = document.getElementById('fileMeta');
                    if (metaEl) metaEl.textContent = '（共 ' + (res.count || 0) + ' 个 · 总计 ' + (res.totalSize || '0 B') + '）';
                    _doRenderFiles();
                }).catch(function() {
                    grid.innerHTML = '<div style="grid-column:1/-1;text-align:center;padding:30px;color:var(--text-muted);opacity:0.7">加载失败，请检查服务器配置</div>';
                });
        }

        function _doRenderFiles() {
            var grid = document.getElementById('fileGrid');
            if (!grid) return;
            var filter = 'all';
            var rb = document.querySelector('input[name="fileFilter"]:checked');
            if (rb) filter = rb.value;
            var list = _fileCache.filter(function(f) {
                if (filter === 'all') return true;
                return f.type === filter;
            });
            if (!list.length) {
                grid.innerHTML = '<div style="grid-column:1/-1;text-align:center;padding:30px;color:var(--text-muted);opacity:0.6">暂无文件</div>';
                return;
            }
            grid.innerHTML = list.map(function(f) {
                var preview = '';
                if (f.type === 'image') {
                    preview = '<img src="' + f.url + '" alt="" style="width:100%;height:120px;object-fit:cover;display:block" onerror="imgLoadError(this)">';
                } else if (f.type === 'audio') {
                    preview = '<div style="width:100%;height:120px;display:flex;align-items:center;justify-content:center;background:var(--glass-bg);flex-direction:column;gap:6px"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" style="width:34px;height:34px;color:#0a84ff;opacity:0.7"><path d="M9 18V5l12-2v13"/><circle cx="6" cy="18" r="3"/><circle cx="18" cy="16" r="3"/></svg><span style="font-size:11px;color:var(--text-muted)">.' + (f.ext || '') + ' 音频</span></div>';
                } else {
                    preview = '<div style="width:100%;height:120px;display:flex;align-items:center;justify-content:center;background:var(--glass-bg);font-size:11px;color:var(--text-muted)">.' + (f.ext || '') + '</div>';
                }
                var escapedName = f.name.replace(/'/g, "\\'").replace(/"/g, '&quot;');
                return '<div class="glass-card file-card" style="padding:8px;margin:0;overflow:hidden">' +
                    preview +
                    '<div style="padding:8px 4px 4px">' +
                    '<div style="font-size:12px;font-weight:600;color:var(--icon-active);white-space:nowrap;overflow:hidden;text-overflow:ellipsis" title="' + f.name + '">' + f.name + '</div>' +
                    '<div style="display:flex;justify-content:space-between;align-items:center;margin-top:4px;font-size:11px;color:var(--text-muted)"><span>' + f.size + '</span><span style="opacity:0.7">' + (f.mtime || '').slice(5, 16) + '</span></div>' +
                    '<div style="display:flex;gap:6px;margin-top:8px">' +
                    '<a href="' + f.url + '" target="_blank" class="btn btn-sm btn-secondary" style="flex:1;text-align:center;text-decoration:none;justify-content:center"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:12px;height:12px"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg> 查看</a>' +
                    '<button class="btn btn-sm btn-danger" onclick="delFile(\'' + escapedName + '\')" style="flex:1"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:12px;height:12px"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg> 删除</button>' +
                    '</div></div></div>';
            }).join('');
        }

        // 图片加载失败时替换为占位符
        function imgLoadError(img) {
            var ph = document.createElement('div');
            ph.style.cssText = 'width:100%;height:120px;display:flex;align-items:center;justify-content:center;background:var(--glass-bg);font-size:11px;color:var(--text-muted)';
            ph.textContent = '无法预览';
            if (img.parentNode) img.parentNode.replaceChild(ph, img);
        }

        function delFile(name) {
            if (!confirm('确定删除文件「' + name + '」？删除后无法恢复！')) return;
            fetch('api.php?action=delete_file', {
                method: 'POST', headers: { 'Content-Type': 'application/json' },
                credentials: 'include', body: JSON.stringify({ name: name })
            }).then(function(res) { return res.json(); })
            .then(function(res) {
                if (res.code === 0) { toast('已删除'); renderFiles(); }
                else { toast(res.msg || '删除失败'); }
            }).catch(function() { toast('请求失败'); });
        }

        // 文件过滤器切换
        document.addEventListener('change', function(e) {
            if (e.target && e.target.name === 'fileFilter') _doRenderFiles();
        });

        /* =========================================================
           🖼️ 卡片管理 - 首页卡片 & 推荐卡片
           ========================================================= */
        var currentCardType = 'home';

        document.addEventListener('click', function(e) {
            var tab = e.target.closest('#cardTabs .chart-tab');
            if (tab) {
                document.querySelectorAll('#cardTabs .chart-tab').forEach(function(t) { t.classList.remove('active'); });
                tab.classList.add('active');
                currentCardType = tab.dataset.type;
                renderCards();
            }
        });

        // 卡片列表直接引用（避免 NavSettings 缓存竞态问题）
        var _cardListRef = { home: [], recommend: [] };

        function _refreshCardListRefs() {
            _cardListRef.home = NavSettings.get('homeCards') || [];
            _cardListRef.recommend = NavSettings.get('recommendCards') || [];
        }

        function saveCard() {
            var title = document.getElementById('cardTitle').value.trim();
            var url = document.getElementById('cardUrl').value.trim();
            var icon = document.getElementById('cardIcon').value.trim();
            var desc = document.getElementById('cardDesc').value.trim();
            var tags = [];
            if (document.getElementById('tagHot').checked) tags.push('hot');
            if (document.getElementById('tagTool').checked) tags.push('tool');
            if (document.getElementById('tagBeauty').checked) tags.push('beauty');
            var customStr = document.getElementById('cardCustomTags').value.trim();
            if (customStr) {
                customStr.split(/[,，]/).forEach(function(t) {
                    var trimmed = t.trim();
                    if (trimmed && tags.indexOf(trimmed) < 0) tags.push(trimmed);
                });
            }

            if (!title) { toast('请输入网站名称'); return; }
            if (!url) { toast('请输入链接地址'); return; }

            var item = {
                id: NavSettings.uid(),
                title: title,
                url: url,
                icon: icon,
                desc: desc,
                tags: tags
            };

            var key = currentCardType === 'home' ? 'homeCards' : 'recommendCards';
            var list = NavSettings.get(key) || [];
            list.push(item);
            // 直接更新引用，确保 renderCards 立即能看到
            if (key === 'homeCards') _cardListRef.home = list;
            else _cardListRef.recommend = list;
            NavSettings.set(key, list);

            var apiAction = currentCardType === 'home' ? 'home_cards' : 'recommend_cards';
            fetch('api.php?action=save_' + apiAction, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                credentials: 'include',
                body: JSON.stringify({ cards: list })
            }).catch(function() {});

            ['cardTitle', 'cardUrl', 'cardDesc', 'cardCustomTags'].forEach(function(id) { document.getElementById(id).value = ''; });
            document.getElementById('cardIcon').value = '';
            clearPreview('cardIconPreview');
            document.getElementById('tagHot').checked = false;
            document.getElementById('tagTool').checked = false;
            document.getElementById('tagBeauty').checked = false;
            toast('卡片已添加');
            renderCards();
        }

        function renderCards() {
            var key = currentCardType === 'home' ? 'homeCards' : 'recommendCards';
            // 优先用直接引用，再回退到 NavSettings
            var list = (key === 'homeCards' ? _cardListRef.home : _cardListRef.recommend);
            if (!Array.isArray(list) || !list.length) {
                list = NavSettings.get(key) || [];
                if (key === 'homeCards') _cardListRef.home = list;
                else _cardListRef.recommend = list;
            }
            var el = document.getElementById('cardsList');
            var countEl = document.getElementById('cardCount');
            if (countEl) countEl.textContent = '（共 ' + list.length + ' 个）';
            if (!list.length) {
                el.innerHTML = '<tr><td colspan="5" class="empty-state">暂无卡片，请添加</td></tr>';
                return;
            }
            el.innerHTML = '<thead><tr><th>图标</th><th>名称</th><th>链接</th><th>标签</th><th>操作</th></tr></thead><tbody>' +
                list.map(function(c, i) {
                    var iconHtml = c.icon ? '<img class="thumb" src="' + c.icon + '" onerror="this.style.display=\'none\'">' : '-';
                    var tagLabels = { hot: '热门', tool: '工具', beauty: '美化' };
                    var tagColors = { hot: 'color:#ff3b30', tool: 'color:#0a84ff', beauty: 'color:#bf5af2' };
                    var tagsHtml = (c.tags || []).map(function(t) {
                        var cls = tagColors[t] || 'color:var(--text-muted)';
                        var label = tagLabels[t] || t;
                        return '<span style="font-size:10px;padding:1px 5px;border-radius:99px;background:rgba(0,0,0,0.08);' + cls + '">' + label + '</span>';
                    }).join(' ');
                    return '<tr><td>' + iconHtml + '</td>' +
                        '<td><b>' + c.title + '</b></td>' +
                        '<td style="max-width:120px;overflow:hidden;text-overflow:ellipsis">' + (c.url || '') + '</td>' +
                        '<td>' + tagsHtml + '</td>' +
                        '<td class="col-actions">' +
                        '<button class="btn btn-sm btn-secondary" onclick="editCard(' + i + ')">编辑</button>' +
                        '<button class="btn btn-sm btn-danger" onclick="delCard(' + i + ')">删除</button></td></tr>';
                }).join('') + '</tbody>';
        }

        function editCard(i) {
            var key = currentCardType === 'home' ? 'homeCards' : 'recommendCards';
            var list = (key === 'homeCards' ? _cardListRef.home : _cardListRef.recommend);
            if (!Array.isArray(list)) list = NavSettings.get(key) || [];
            var c = list[i];
            if (!c) return;

            var presetTags = ['hot', 'tool', 'beauty'];
            var tagsHtml = presetTags.map(function(t) {
                var checked = (c.tags || []).indexOf(t) >= 0 ? ' checked' : '';
                var label = t === 'hot' ? '热门' : t === 'tool' ? '工具' : '设计';
                var cls = t === 'hot' ? 'color:#ff3b30' : t === 'tool' ? 'color:#0a84ff' : 'color:#bf5af2';
                return '<label style="display:flex;align-items:center;gap:4px;font-size:11px;cursor:pointer">' +
                    '<input type="checkbox" id="mTag_' + t + '"' + checked + '> ' +
                    '<span style="padding:2px 8px;border-radius:99px;background:rgba(0,0,0,0.08);' + cls + '">' + label + '</span></label>';
            }).join('');
            // 提取自定义标签（非预设的）
            var customTags = (c.tags || []).filter(function(t) { return presetTags.indexOf(t) < 0; });

            openModal('编辑卡片',
                '<div class="form-row">' +
                '<div class="form-group"><label class="form-label">网站名称</label><input class="glass-input" id="mTitle" value="' + c.title + '"></div>' +
                '<div class="form-group"><label class="form-label">链接地址</label><input class="glass-input" id="mUrl" value="' + (c.url || '') + '"></div>' +
                '</div>' +
                '<div class="form-group"><label class="form-label">图标URL</label><input class="glass-input" id="mIcon" value="' + (c.icon || '') + '"></div>' +
                '<div class="form-group"><label class="form-label">简介</label><input class="glass-input" id="mDesc" value="' + (c.desc || '') + '"></div>' +
                '<div class="form-group"><label class="form-label">标签</label><div style="display:flex;gap:8px;flex-wrap:wrap;margin-top:4px">' + tagsHtml + '</div>' +
                '<input class="glass-input" id="mCustomTags" value="' + customTags.join(', ') + '" placeholder="自定义标签，逗号分隔" style="margin-top:8px;font-size:12px"></div>',
                function() {
                    c.title = document.getElementById('mTitle').value.trim();
                    c.url = document.getElementById('mUrl').value.trim();
                    c.icon = document.getElementById('mIcon').value.trim();
                    c.desc = document.getElementById('mDesc').value.trim();
                    c.tags = [];
                    ['hot', 'tool', 'beauty'].forEach(function(t) {
                        if (document.getElementById('mTag_' + t).checked) c.tags.push(t);
                    });
                    // 自定义标签
                    var mCustom = document.getElementById('mCustomTags').value.trim();
                    if (mCustom) {
                        mCustom.split(/[,，]/).forEach(function(t) {
                            var trimmed = t.trim();
                            if (trimmed && c.tags.indexOf(trimmed) < 0) c.tags.push(trimmed);
                        });
                    }
                    list[i] = c;
                    if (key === 'homeCards') _cardListRef.home = list;
                    else _cardListRef.recommend = list;
                    NavSettings.set(key, list);

                    // 同步到后端
                    fetch('api.php?action=save_' + (currentCardType === 'home' ? 'home_cards' : 'recommend_cards'), {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        credentials: 'include',
                        body: JSON.stringify({ cards: list })
                    }).catch(function() {});

                    renderCards();
                    closeModal();
                    toast('已更新');
                });
        }

        function delCard(i) {
            if (!confirm('确定删除这个卡片？')) return;
            var key = currentCardType === 'home' ? 'homeCards' : 'recommendCards';
            var list = (key === 'homeCards' ? _cardListRef.home : _cardListRef.recommend);
            if (!Array.isArray(list)) list = NavSettings.get(key) || [];
            list.splice(i, 1);
            if (key === 'homeCards') _cardListRef.home = list;
            else _cardListRef.recommend = list;
            NavSettings.set(key, list);

            // 同步到后端
            fetch('api.php?action=save_' + (currentCardType === 'home' ? 'home_cards' : 'recommend_cards'), {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                credentials: 'include',
                body: JSON.stringify({ cards: list })
            }).catch(function() {});

            renderCards();
            toast('已删除');
        }

        /* =========================================================
           ⚙️ 设置 & 控制中心
           ========================================================= */
        var TOGGLE_ITEMS = [
            { key: 'showMusicList', icon: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 18V5l12-2v13"/><circle cx="6" cy="18" r="3"/><circle cx="18" cy="16" r="3"/></svg>', name: '音乐列表' },
            { key: 'showProgressPanel', icon: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>', name: '进度面板' },
            { key: 'showNews', icon: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 22h16a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2H8a2 2 0 0 0-2 2v16a2 2 0 0 1-2 2Zm0 0a2 2 0 0 1-2-2v-9c0-1.1.9-2 2-2h2"/><path d="M18 14h-8"/><path d="M15 18h-5"/><path d="M10 6h8v4h-8V6Z"/></svg>', name: '新闻头条' },
            { key: 'showSearchTime', icon: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>', name: '时钟' },
            { key: 'showDailyQuote', icon: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>', name: '每日一言' }
        ];

        // 渲染控制中心
        function renderControlPanel() {
            var panel = document.getElementById('controlPanel');
            if (!panel) return;
            var s = NavSettings.getAll();
            panel.innerHTML = '<div class="ctrl-grid">' +
                TOGGLE_ITEMS.map(function(t) {
                    var active = !!s[t.key];
                    return '<div class="ctrl-item' + (active ? ' active' : '') + '" data-key="' + t.key +
                        '">' +
                        '<span class="ctrl-icon">' + t.icon + '</span>' +
                        '<span class="ctrl-name">' + t.name + '</span>' +
                        '</div>';
                }).join('') +
                '</div>';
            // 点击切换
            panel.querySelectorAll('.ctrl-item').forEach(function(el) {
                el.addEventListener('click', function(e) {
                    e.stopPropagation();
                    var key = this.dataset.key;
                    var s2 = NavSettings.getAll();
                    var val = !s2[key];
                    NavSettings.set(key, val);
                    this.classList.toggle('active', val);
                    toast(val ? '已开启' : '已关闭');
                    // 同步设置页（如果有）
                    var settingCb = document.querySelector('#toggleList input[data-key="' + key + '"]');
                    if (settingCb) settingCb.checked = val;
                });
            });
        }

        function renderSettings() {
            var s = NavSettings.getAll();
            // 站点信息
            document.getElementById('siteTitle').value = s.siteTitle || '';
            document.getElementById('siteDesc').value = s.siteDesc || '';
            document.getElementById('siteFavicon').value = s.siteFavicon || '';
            var fp = document.getElementById('siteFaviconPreview');
            if (s.siteFavicon) { fp.src = s.siteFavicon;
                fp.classList.add('show');
                showDelBtn('siteFaviconPreview'); } else { fp.classList.remove('show');
                hideDelBtn('siteFaviconPreview'); }
            // 账号
            var acc = JSON.parse(localStorage.getItem('nav_admin_account') || '{"username":"admin"}');
            document.getElementById('accUser').value = acc.username || '';
        }

        function saveSiteInfo() {
            var title = document.getElementById('siteTitle').value.trim();
            var desc = document.getElementById('siteDesc').value.trim();
            var favicon = document.getElementById('siteFavicon').value.trim();
            NavSettings.setAll({ siteTitle: title, siteDesc: desc, siteFavicon: favicon });
            // 同步到后端
            fetch('api.php?action=save_settings', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                credentials: 'include',
                body: JSON.stringify({ siteTitle: title, siteDesc: desc, siteFavicon: favicon })
            }).then(function(res) { return res.json(); })
            .then(function(data) {
                if (data.code === 0) toast('已保存');
                else toast(data.msg || '保存失败');
            }).catch(function() { toast('已保存（离线）'); });
        }

        function saveAccount() {
            var user = document.getElementById('accUser').value.trim();
            var old = document.getElementById('accOld').value;
            var nw = document.getElementById('accNew').value;
            var repass = document.getElementById('accRepass').value;
            if (!user) { toast('请输入用户名'); return; }
            if (!old) { toast('请输入原密码'); return; }
            if (!nw) { toast('请输入新密码'); return; }
            if (nw !== repass) { toast('两次密码不一致'); return; }
            fetch('api.php?action=change_password', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                credentials: 'include',
                body: JSON.stringify({ username: user, old_password: old, new_password: nw, re_password: repass })
            }).then(function(res) { return res.json(); })
            .then(function(data) {
                if (data.code === 0) {
                    // 更新本地记录
                    localStorage.setItem('nav_admin_account', JSON.stringify({ username: user, password: nw }));
                    document.getElementById('accOld').value = '';
                    document.getElementById('accNew').value = '';
                    document.getElementById('accRepass').value = '';
                    toast('账号已更新');
                } else {
                    toast(data.msg || '修改失败');
                }
            }).catch(function() {
                // 离线降级
                var acc = JSON.parse(localStorage.getItem('nav_admin_account') || '{"username":"admin"}');
                if (old !== (acc.password || '')) { toast('原密码错误'); return; }
                acc.username = user;
                acc.password = nw;
                localStorage.setItem('nav_admin_account', JSON.stringify(acc));
                document.getElementById('accOld').value = '';
                document.getElementById('accNew').value = '';
                document.getElementById('accRepass').value = '';
                toast('账号已更新（离线）');
            });
        }

        /* =========================================================
           🖼️ 图片预览 & 删除
           ========================================================= */
        function showPreview(previewId, src) {
            var img = document.getElementById(previewId);
            if (!img) return;
            if (src) { img.src = src;
                img.classList.add('show');
                showDelBtn(previewId); } else { img.classList.remove('show');
                hideDelBtn(previewId); }
        }

        function showDelBtn(previewId) {
            var wrap = document.querySelector('#' + previewId) ? document.querySelector('#' + previewId).closest(
                '.img-preview-wrap') : null;
            if (wrap) { var btn = wrap.querySelector('.del-img-btn'); if (btn) btn.classList.add('show'); }
        }

        function hideDelBtn(previewId) {
            var wrap = document.querySelector('#' + previewId) ? document.querySelector('#' + previewId).closest(
                '.img-preview-wrap') : null;
            if (wrap) { var btn = wrap.querySelector('.del-img-btn'); if (btn) btn.classList.remove('show'); }
        }

        function clearPreview(previewId) {
            var img = document.getElementById(previewId);
            if (img) { img.src = '';
                img.classList.remove('show'); }
            hideDelBtn(previewId);
        }

        function setupDeleteButtons() {
            document.querySelectorAll('.del-img-btn').forEach(function(btn) {
                btn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    var targetId = this.dataset.target;
                    var previewId = this.dataset.preview;
                    var input = document.getElementById(targetId);
                    if (input) input.value = '';
                    clearPreview(previewId);
                    toast('已删除');
                });
                var timer = null;
                btn.addEventListener('touchstart', function(e) {
                    timer = setTimeout(function() { e.preventDefault();
                        btn.click(); }, 500);
                }, { passive: true });
                btn.addEventListener('touchend', function() { clearTimeout(timer); });
                btn.addEventListener('touchmove', function() { clearTimeout(timer); });
            });
        }

        /* =========================================================
           🚀 初始化
           ========================================================= */
        function initUploadButtons() {
            document.querySelectorAll('[data-target]').forEach(function(btn) {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    var targetId = this.dataset.target;
                    var input = document.getElementById(targetId);
                    if (!input) return;
                    var fi = document.createElement('input');
                    fi.type = 'file';
                    fi.accept = this.dataset.accept || '*/*';
                    fi.style.display = 'none';
                    var preview = this.dataset.preview === 'true';
                    var previewId = targetId + 'Preview';
                    fi.addEventListener('change', function() {
                        var f = fi.files[0];
                        if (!f) return;
                        // 上传到服务器 uploads 文件夹（不再用 base64 避免卡死）
                        var formData = new FormData();
                        formData.append('file', f);
                        // 显示上传中状态
                        var oldText = btn.innerHTML;
                        btn.innerHTML = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 12a9 9 0 1 1-6.219-8.56"/></svg> 上传中...';
                        btn.disabled = true;
                        fetch('api.php?action=upload', {
                            method: 'POST',
                            credentials: 'include',
                            body: formData
                        }).then(function(res) { return res.json(); })
                        .then(function(data) {
                            btn.innerHTML = oldText;
                            btn.disabled = false;
                            if (data.code === 0 && data.url) {
                                input.value = data.url;
                                if (preview) showPreview(previewId, data.url);
                                else input.dispatchEvent(new Event('input'));
                                toast('上传成功');
                            } else {
                                toast(data.msg || '上传失败');
                            }
                        }).catch(function() {
                            btn.innerHTML = oldText;
                            btn.disabled = false;
                            // 降级：小文件用 base64（仅当 fetch 失败时）
                            if (f.size < 512 * 1024) {
                                var r = new FileReader();
                                r.onload = function() {
                                    input.value = r.result;
                                    if (preview) showPreview(previewId, r.result);
                                    else input.dispatchEvent(new Event('input'));
                                };
                                r.readAsDataURL(f);
                                toast('API不可用，已使用本地缓存');
                            } else {
                                toast('上传失败，请检查网络或服务器配置');
                            }
                        });
                        if (fi.parentNode) fi.parentNode.removeChild(fi);
                    });
                    document.body.appendChild(fi);
                    fi.click();
                });
            });
            ['musicCover', 'newsImage', 'siteFavicon'].forEach(function(id) {
                var input = document.getElementById(id);
                var previewId = id + 'Preview';
                if (input) {
                    input.addEventListener('input', function() {
                        var val = this.value.trim();
                        if (val) showPreview(previewId, val);
                        else clearPreview(previewId);
                    });
                    if (input.value.trim()) showPreview(previewId, input.value.trim());
                }
            });
            setupDeleteButtons();
        }

        /* ---------- 模态框 ---------- */
        function openModal(title, html, onOk, opts) {
            opts = opts || {};
            document.getElementById('modalTitle').textContent = title;
            document.getElementById('modalBody').innerHTML = html;
            document.getElementById('modal').classList.add('show');
            var okBtn = document.getElementById('modalOk');
            var cancelBtn = document.querySelector('.modal-actions .btn-secondary');
            // 重置按钮状态（避免上次的 onlyOk 设置残留）
            if (cancelBtn) cancelBtn.style.display = '';
            okBtn.textContent = opts.okText || '确定';
            okBtn.onclick = onOk || closeModal;
            if (opts.onlyOk && cancelBtn) cancelBtn.style.display = 'none';
        }

        function closeModal() { document.getElementById('modal').classList.remove('show'); }
        document.getElementById('modal').addEventListener('click', function(e) { if (e.target.id === 'modal')
            closeModal(); });

        /* ---------- 启动 ---------- */
        document.addEventListener('DOMContentLoaded', function() {
            // 登录验证
            checkAuth().then(function(ok) {
                if (!ok) return;

                // 从后端加载数据
                return NavSettings.init();
            }).then(function() {
                // 数据加载完成后初始化
                var notices = NavSettings.get('notices') || [];
                if (notices.length === 0) {
                    NavSettings.set('notices', [{
                        id: NavSettings.uid(),
                        title: '🎉 液态玻璃后台已上线',
                        content: '欢迎使用后台管理面板，支持公告、音乐、头条、进度等管理。',
                        url: 'https://kxlove.top',
                        date: new Date().toLocaleDateString('zh-CN')
                    }]);
                }

                initUploadButtons();

                var acc = JSON.parse(localStorage.getItem('nav_admin_account') || '{}');
                if (!acc.username) {
                    localStorage.setItem('nav_admin_account', JSON.stringify({ username: 'admin', password: 'admin123' }));
                }

                // 初始化控制面板
                renderControlPanel();
                renderSettings();

                switchView('dashboard');
                setTimeout(function() {
                    var active = document.querySelector('.nav-btn.active');
                    if (active) updatePill(active, false);
                }, 80);

                // 控制中心展开/收起
                var controlIsland = document.getElementById('control-island');
                var controlPanel = document.getElementById('controlPanel');
                if (controlIsland) {
                    controlIsland.addEventListener('click', function(e) {
                        if (e.target.closest('.ctrl-item') || e.target.closest('.ctrl-grid')) return;
                        var expanded = this.classList.toggle('expanded');
                        this.setAttribute('aria-expanded', String(expanded));
                        if (controlPanel) controlPanel.setAttribute('aria-hidden', String(!expanded));
                    });
                    document.addEventListener('click', function(e) {
                        if (!controlIsland.contains(e.target) && controlIsland.classList.contains('expanded')) {
                            controlIsland.classList.remove('expanded');
                            controlIsland.setAttribute('aria-expanded', 'false');
                            if (controlPanel) controlPanel.setAttribute('aria-hidden', 'true');
                        }
                    });
                    document.addEventListener('keydown', function(e) {
                        if (e.key === 'Escape' && controlIsland.classList.contains('expanded')) {
                            controlIsland.classList.remove('expanded');
                            controlIsland.setAttribute('aria-expanded', 'false');
                            if (controlPanel) controlPanel.setAttribute('aria-hidden', 'true');
                        }
                    });
                }

                // 退出登录按钮绑定（添加到主题按钮旁）
                var themeBtn = document.getElementById('themeBtn');
                if (themeBtn) {
                    var logoutBtn = document.createElement('button');
                    logoutBtn.className = 'theme-btn';
                    logoutBtn.title = '退出登录';
                    logoutBtn.innerHTML = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:18px;height:18px;"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>';
                    logoutBtn.addEventListener('click', logout);
                    themeBtn.parentNode.insertBefore(logoutBtn, themeBtn.nextSibling);
                }
            }).catch(function() {
                // 离线降级：直接加载
                var notices = NavSettings.get('notices') || [];
                if (notices.length === 0) {
                    NavSettings.set('notices', [{
                        id: NavSettings.uid(),
                        title: '🎉 液态玻璃后台已上线（离线模式）',
                        content: '当前处于离线模式，数据仅保存在本地。',
                        url: '',
                        date: new Date().toLocaleDateString('zh-CN')
                    }]);
                }
                initUploadButtons();
                renderControlPanel();
                renderSettings();
                switchView('dashboard');
                setTimeout(function() {
                    var active = document.querySelector('.nav-btn.active');
                    if (active) updatePill(active, false);
                }, 80);
            });
        });
    </script>
</body>
</html>