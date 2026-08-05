<!-- 
  ============================================================================
  液态玻璃导航栏 - 参考 Apple Liquid Glass 设计 (更新 SVG 液态扭曲效果)
  ============================================================================
-->
<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>液态玻璃导航栏</title>
  <link rel="stylesheet" href="layui/css/layui.css" media="all">
  <script src="layui/layui.js"></script>
</head>
<style>
  /* ============================================
     搜索区域容器 - 垂直排列
     ============================================ */
  .search-wrapper {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 16px;
    position: relative;
    z-index: 10;
    transform: translateY(-3vh);
  }

  /* 手机端减小上移量，避免下方空白过多 */
  @media (max-width: 768px) {
    .search-wrapper {
      transform: translateY(0vh);
    }
  }

  .engine-selector {
    position: relative;
    display: flex;
    align-items: center;
    padding: 6px;
    border-radius: 99px;
    /* 替换为新的液态玻璃效果 */
    background: rgba(255,255,255, 0.1);
    backdrop-filter: blur(2px) url(#liquid_glass_filter);
    -webkit-backdrop-filter: blur(2px) url(#liquid_glass_filter);
    box-shadow:
      inset 0 1px 0 rgba(255,255,255,0.45),
      inset 0 -1px 0 rgba(255,255,255,0.18),
      inset 6px 6px 16px rgba(255,255,255,0.12),
      0 10px 28px rgba(0,0,0,0.35);
    transition: all 0.5s ease;
    z-index: 10;
  }

  .engine-items {
    position: relative;
    display: flex;
    gap: 2px;
    z-index: 2;
  }

  .engine-pill {
    position: absolute;
    top: 0; left: 0;
    height: 36px;
    background: var(--pill-bg);
    border-radius: 99px;
    box-shadow: var(--pill-shadow);
    transition: transform 0.5s cubic-bezier(0.34, 1.2, 0.64, 1),
                width 0.5s cubic-bezier(0.34, 1.2, 0.64, 1),
                background 0.5s ease, box-shadow 0.5s ease;
    z-index: 1;
  }

  .engine-btn {
    position: relative;
    background: transparent;
    border: none;
    padding: 0 14px;
    height: 36px;
    border-radius: 99px;
    font-family: inherit;
    font-size: 13px;
    font-weight: 600;
    color: var(--icon-color);
    cursor: pointer;
    -webkit-tap-highlight-color: transparent;
    transition: color 0.3s ease;
    outline: none;
    z-index: 2;
    white-space: nowrap;
  }

  .engine-btn:hover, .engine-btn.active {
    color: var(--icon-active);
  }

  .engine-btn .btn-content {
    display: flex;
    align-items: center;
    gap: 6px;
    pointer-events: none;
    transition: transform 0.2s cubic-bezier(0.32, 0.72, 0, 1);
  }

  .engine-btn:active .btn-content {
    transform: scale(0.92);
  }

  .engine-btn svg {
    flex-shrink: 0;
  }

  /* ============================================
     网站推荐卡片 - 液态玻璃风格
     ============================================ */
  .site-cards {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 14px;
    width: 600px;
    max-width: 90vw;
    margin-top: 30px;
    z-index: 10;
    /* 限制卡片区域最大高度，超出则自身滚动，避免撑开搜索框位置 */
    max-height: 52vh;
    overflow-y: auto;
    padding: 4px;
    box-sizing: border-box;
  }
  /* 美化卡片区域滚动条 */
  .site-cards::-webkit-scrollbar { width: 5px; }
  .site-cards::-webkit-scrollbar-track { background: transparent; }
  .site-cards::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.25); border-radius: 99px; }
  .site-cards::-webkit-scrollbar-thumb:hover { background: rgba(255,255,255,0.4); }
  .site-card {
    position: relative;
    display: flex;
    flex-direction: column;
    gap: 10px;
    padding: 18px;
    border-radius: 20px;
    background: var(--glass-bg);
    backdrop-filter: blur(20px) saturate(180%);
    -webkit-backdrop-filter: blur(20px) saturate(180%);
    border: 1px solid var(--glass-border);
    box-shadow:
      inset 0 1px 0 rgba(255,255,255,0.4),
      0 8px 24px rgba(0,0,0,0.12);
    text-decoration: none;
    color: var(--icon-active);
    overflow: hidden;
    cursor: pointer;
    transition: transform 0.35s cubic-bezier(0.34, 1.4, 0.5, 1),
                box-shadow 0.35s ease;
  }
  .site-card:hover {
    transform: translateY(-4px);
    box-shadow:
      inset 0 1px 0 rgba(255,255,255,0.5),
      0 14px 32px rgba(0,0,0,0.2);
  }
  .site-card:active { transform: translateY(-2px) scale(0.98); }
  .site-card .card-head { display: flex; align-items: center; gap: 12px; }
  .site-card .card-icon {
    width: 40px; height: 40px;
    border-radius: 12px;
    flex-shrink: 0;
    background: rgba(255,255,255,0.3);
    display: flex; align-items: center; justify-content: center;
  }
  .site-card .card-icon img { width: 30px; height: 30px; border-radius: 6px; }
  .site-card .card-info { min-width: 0; flex: 1; }
  .site-card .card-title {
    font-size: 15px; font-weight: 600;
    color: var(--icon-active);
    white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
    margin-bottom: 4px;
  }
  .site-card .card-tags { display: flex; gap: 4px; flex-wrap: wrap; }
  .site-card .card-tag {
    font-size: 10px;
    padding: 2px 7px;
    border-radius: 99px;
    background: rgba(255,255,255,0.25);
    color: var(--icon-color);
    border: 1px solid var(--glass-border);
  }
  .site-card .card-tag.hot { background: rgba(255,69,58,0.18); color: #ff3b30; border-color: rgba(255,69,58,0.3); }
  .site-card .card-tag.tool { background: rgba(0,122,255,0.18); color: #0a84ff; border-color: rgba(0,122,255,0.3); }
  .site-card .card-tag.beauty { background: rgba(191,90,242,0.18); color: #bf5af2; border-color: rgba(191,90,242,0.3); }
  .site-card .card-desc {
    font-size: 12px; line-height: 1.5;
    color: var(--icon-color);
    opacity: 0.85;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
  }
  .site-card .card-visit {
    display: inline-flex; align-items: center; gap: 4px;
    align-self: flex-start;
    font-size: 12px; font-weight: 600;
    padding: 6px 14px;
    border-radius: 12px;
    background: rgba(0,122,255,0.12);
    color: #0a84ff;
    border: 1px solid rgba(0,122,255,0.25);
    transition: background 0.3s ease;
  }
  .site-card:hover .card-visit { background: rgba(0,122,255,0.22); }
  .site-card .ripple {
    position: absolute; border-radius: 50%;
    background: rgba(255,255,255,0.5);
    transform: scale(0); animation: cardRipple 0.6s ease-out;
    pointer-events: none;
  }
  @keyframes cardRipple { to { transform: scale(4); opacity: 0; } }
  /* 手机端：紧凑横排，隐藏简介和按钮 */
  @media (max-width: 600px) {
    .site-cards { grid-template-columns: repeat(2, 1fr); gap: 10px; margin-top: 6px; }
    .site-card { flex-direction: row; align-items: center; padding: 12px; gap: 10px; }
    .site-card .card-head { gap: 10px; }
    .site-card .card-icon { width: 32px; height: 32px; }
    .site-card .card-desc, .site-card .card-visit { display: none; }
  }

  /* 卡片切换过渡 */
  .site-cards { transition: opacity 0.22s ease, transform 0.22s ease; }
  .site-cards.swapping { opacity: 0; transform: translateY(10px); }

  /* 加载更多 / 到底提示 */
  .load-more-wrap {
    grid-column: 1 / -1;
    display: flex;
    justify-content: center;
    align-items: center;
    padding: 8px 0 4px;
  }
  .load-more-btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 10px 22px;
    border-radius: 99px;
    border: 1px solid var(--glass-border);
    background: var(--glass-bg);
    backdrop-filter: blur(20px) saturate(180%);
    -webkit-backdrop-filter: blur(20px) saturate(180%);
    color: var(--icon-active);
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    transition: transform 0.3s cubic-bezier(0.34, 1.4, 0.5, 1), background 0.3s ease, box-shadow 0.3s ease;
    box-shadow: 0 4px 14px rgba(0,0,0,0.1);
  }
  .load-more-btn:hover {
    transform: translateY(-2px);
    background: rgba(0,122,255,0.14);
    box-shadow: 0 8px 20px rgba(0,0,0,0.16);
  }
  .load-more-btn:active { transform: translateY(0) scale(0.97); }
  .load-more-btn svg { width: 15px; height: 15px; }
  .load-more-btn.loading svg { animation: spin 0.8s linear infinite; }
  @keyframes spin { to { transform: rotate(360deg); } }
  .no-more {
    font-size: 12px;
    color: var(--icon-color);
    opacity: 0.6;
    padding: 6px 0;
  }

  /* ============================================
     添加推荐表单 - 液态玻璃风格
     ============================================ */
  .add-form {
    width: 600px;
    max-width: 90vw;
    margin-top: 50px;
    padding: 24px;
    border-radius: 24px;
    background: var(--glass-bg);
    backdrop-filter: blur(20px) saturate(180%);
    -webkit-backdrop-filter: blur(20px) saturate(180%);
    border: 1px solid var(--glass-border);
    box-shadow:
      inset 0 1px 0 rgba(255,255,255,0.4),
      0 8px 24px rgba(0,0,0,0.12);
    z-index: 10;
    display: none;
    opacity: 0;
    transform: translateY(10px);
    transition: opacity 0.3s ease, transform 0.3s cubic-bezier(0.34, 1.4, 0.5, 1);
  }
  .add-form.show { opacity: 1; transform: translateY(0); }
  .add-form-header {
    display: flex; align-items: center; justify-content: space-between;
    margin-bottom: 14px;
  }
  .add-form-title {
    display: flex; align-items: center; gap: 8px;
    font-size: 16px; font-weight: 700;
    color: var(--icon-active);
  }
  .add-form-title svg { width: 18px; height: 18px; fill: #34c759; }
  .add-form-hint {
    font-size: 12px;
    color: var(--icon-color);
    background: rgba(255,255,255,0.2);
    padding: 2px 10px;
    border-radius: 99px;
    border: 1px solid var(--glass-border);
  }
  .add-form-textarea {
    width: 100%;
    min-height: 140px;
    padding: 14px 16px;
    border-radius: 16px;
    background: rgba(255,255,255,0.08);
    border: 1px solid var(--glass-border);
    color: var(--icon-active);
    font-family: inherit;
    font-size: 14px;
    line-height: 1.6;
    resize: vertical;
    outline: none;
    transition: background 0.3s ease, border-color 0.3s ease, box-shadow 0.3s ease;
  }
  .add-form-textarea::placeholder { color: var(--icon-color); opacity: 0.7; }
  .add-form-textarea:focus {
    background: rgba(255,255,255,0.12);
    border-color: rgba(10,122,255,0.5);
    box-shadow: 0 0 0 3px rgba(10,122,255,0.15);
  }
  .add-form-actions { display: flex; gap: 12px; margin-top: 14px; }
  .add-form-btn {
    flex: 1;
    padding: 12px 20px;
    border: none;
    border-radius: 14px;
    font-family: inherit;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: transform 0.3s cubic-bezier(0.34, 1.4, 0.5, 1), background 0.3s ease;
  }
  .add-form-btn:active { transform: scale(0.96); }
  .add-form-btn-submit {
    background: #0a84ff; color: #fff;
    box-shadow: 0 6px 18px -4px rgba(10,122,255,0.5);
  }
  .add-form-btn-submit:hover { background: #0066cc; }
  .add-form-btn-cancel {
    background: rgba(120,120,128,0.2);
    color: var(--icon-active);
    border: 1px solid var(--glass-border);
  }
  .add-form-btn-cancel:hover { background: rgba(120,120,128,0.3); }

  /* 提交提示 Toast */
  .add-toast {
    position: fixed;
    bottom: 96px;
    left: 50%;
    transform: translate(-50%, 20px);
    padding: 12px 22px;
    border-radius: 99px;
    background: var(--glass-bg);
    backdrop-filter: blur(20px) saturate(180%);
    -webkit-backdrop-filter: blur(20px) saturate(180%);
    border: 1px solid var(--glass-border);
    box-shadow: inset 0 1px 0 rgba(255,255,255,0.4), 0 8px 24px rgba(0,0,0,0.18);
    color: var(--icon-active);
    font-size: 14px; font-weight: 600;
    z-index: 10000;
    opacity: 0; pointer-events: none;
    transition: opacity 0.3s ease, transform 0.3s cubic-bezier(0.34, 1.4, 0.5, 1);
  }
  .add-toast.show { opacity: 1; transform: translate(-50%, 0); }

  @media (max-width: 600px) {
    .add-form { margin-top: 6px; padding: 18px; }
  }

  /* ============================================
     时钟 - 推荐卡片下方
     ============================================ */
  .clock-panel {
    display: flex;
    align-items: center;
    gap: 16px;
    margin-bottom: 4px;
    padding: 14px 28px;
    border-radius: 99px;
    background: var(--glass-bg);
    backdrop-filter: blur(20px) saturate(180%);
    -webkit-backdrop-filter: blur(20px) saturate(180%);
    border: 1px solid var(--glass-border);
    box-shadow:
      inset 0 1px 0 rgba(255,255,255,0.4),
      0 8px 24px rgba(0,0,0,0.12);
    z-index: 10;
  }
  @media (max-width: 768px) {
    .clock-panel { margin-top: -4px; padding: 10px 20px; }
    .clock-time { font-size: 22px; }
    .clock-seconds { font-size: 15px; }
    .clock-date { display: none; }
  }
  .clock-time {
    font-size: 28px;
    font-weight: 700;
    color: var(--icon-active);
    font-variant-numeric: tabular-nums;
    letter-spacing: 1px;
    line-height: 1;
  }
  .clock-seconds {
    font-size: 18px;
    font-weight: 600;
    color: #0a84ff;
    font-variant-numeric: tabular-nums;
  }
  .clock-date {
    font-size: 13px;
    color: var(--icon-color);
    line-height: 1.4;
    border-left: 1px solid var(--divider);
    padding-left: 16px;
  }
  .clock-date .clock-week {
    color: var(--icon-active);
    font-weight: 600;
  }

  /* ============================================
     开发进度表 - 固定浮动在左侧
     ============================================ */
  .dev-panel {
    position: fixed;
    left: 24px;
    top: 38%;
    transform: translateY(-50%);
    width: 300px;
    padding: 24px;
    border-radius: 24px;
    background: var(--glass-bg);
    backdrop-filter: blur(20px) saturate(180%);
    -webkit-backdrop-filter: blur(20px) saturate(180%);
    border: 1px solid var(--glass-border);
    box-shadow:
      inset 0 1px 0 rgba(255,255,255,0.4),
      0 8px 24px rgba(0,0,0,0.12);
    max-height: 560px;
    display: flex;
    flex-direction: column;
    z-index: 20;
  }
  .dev-panel.dragging { user-select: none; cursor: grabbing; }
  .dev-header {
    display: flex; align-items: center; justify-content: space-between;
    cursor: grab;
    margin-bottom: 16px;
  }
  .dev-title {
    font-size: 16px; font-weight: 700;
    color: var(--icon-active);
    display: flex; align-items: center; gap: 8px;
  }
  .dev-title svg { width: 18px; height: 18px; fill: #34c759; }
  .dev-date {
    font-size: 12px;
    color: var(--icon-color);
    background: rgba(255,255,255,0.2);
    padding: 2px 10px;
    border-radius: 99px;
    border: 1px solid var(--glass-border);
  }
  .dev-items {
    flex: 1;
    overflow-y: auto;
    display: flex;
    flex-direction: column;
    gap: 14px;
    padding-right: 4px;
  }
  .dev-items::-webkit-scrollbar { width: 4px; }
  .dev-items::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.3); border-radius: 99px; }
  .dev-item {
    display: flex;
    flex-direction: column;
    gap: 6px;
  }
  .dev-item-head {
    display: flex; align-items: center; justify-content: space-between;
  }
  .dev-item-name {
    font-size: 13px; font-weight: 600;
    color: var(--icon-active);
  }
  .dev-item-percent {
    font-size: 12px; font-weight: 700;
    color: #34c759;
  }
  .dev-item-bar {
    height: 6px;
    border-radius: 99px;
    background: rgba(120,120,128,0.2);
    overflow: hidden;
  }
  .dev-item-fill {
    height: 100%;
    border-radius: 99px;
    transition: width 1s cubic-bezier(0.34, 1.2, 0.64, 1);
  }
  .dev-item-fill.done { background: linear-gradient(90deg, #34c759, #30d158); }
  .dev-item-fill.progress { background: linear-gradient(90deg, #0a84ff, #5ac8fa); }
  .dev-item-fill.warning { background: linear-gradient(90deg, #ff9500, #ffb340); }
  .dev-item-fill.pending { background: linear-gradient(90deg, #8e8e93, #aeaeb2); }
  .dev-item-desc {
    font-size: 11px;
    color: var(--icon-color);
    opacity: 0.8;
  }
  .dev-item-status {
    display: inline-flex; align-items: center; gap: 4px;
    font-size: 10px;
    padding: 1px 8px;
    border-radius: 99px;
    border: 1px solid var(--glass-border);
  }
  .dev-item-status.done { background: rgba(52,199,89,0.15); color: #34c759; border-color: rgba(52,199,89,0.3); }
  .dev-item-status.progress { background: rgba(10,122,255,0.15); color: #0a84ff; border-color: rgba(10,122,255,0.3); }
  .dev-item-status.warning { background: rgba(255,149,0,0.15); color: #ff9500; border-color: rgba(255,149,0,0.3); }
  .dev-item-status.pending { background: rgba(142,142,147,0.15); color: #8e8e93; border-color: rgba(142,142,147,0.3); }

  /* ============================================
     每日一言 - 开发进度下方
     ============================================ */
  .quote-panel {
    position: fixed;
    left: 24px;
    top: calc(48% + 320px);
    transform: translateY(-50%);
    width: 300px;
    padding: 20px 24px;
    border-radius: 24px;
    background: var(--glass-bg);
    backdrop-filter: blur(20px) saturate(180%);
    -webkit-backdrop-filter: blur(20px) saturate(180%);
    border: 1px solid var(--glass-border);
    box-shadow:
      inset 0 1px 0 rgba(255,255,255,0.4),
      0 8px 24px rgba(0,0,0,0.12);
    z-index: 20;
  }
  .quote-panel.dragging { user-select: none; cursor: grabbing; }
  .quote-header {
    display: flex; align-items: center; justify-content: space-between;
    cursor: grab;
    margin-bottom: 12px;
  }
  .quote-title {
    font-size: 16px; font-weight: 700;
    color: var(--icon-active);
    display: flex; align-items: center; gap: 8px;
  }
  .quote-title svg { width: 18px; height: 18px; fill: #af52de; }
  .quote-date {
    font-size: 12px;
    color: var(--icon-color);
    background: rgba(255,255,255,0.2);
    padding: 2px 10px;
    border-radius: 99px;
    border: 1px solid var(--glass-border);
  }
  .quote-content {
    font-size: 14px;
    color: var(--icon-active);
    line-height: 1.7;
    padding: 16px 14px;
    border-radius: 16px;
    background: rgba(255,255,255,0.08);
    border-left: 3px solid #af52de;
    margin-bottom: 10px;
    position: relative;
  }
  .quote-content::before {
    content: '\201C';
    position: absolute;
    top: -4px;
    left: 8px;
    font-size: 36px;
    color: #af52de;
    opacity: 0.4;
    font-family: Georgia, serif;
    line-height: 1;
  }
  .quote-author {
    font-size: 12px;
    color: var(--icon-color);
    text-align: right;
    opacity: 0.8;
  }

  /* 手机端隐藏 */
  @media (max-width: 900px) {
    .dev-panel, .quote-panel { display: none; }
  }

  /* ============================================
     播放列表 - 固定浮动在右侧
     ============================================ */
  .playlist-panel {
    position: fixed;
    right: 24px;
    top: 25%;
    transform: translateY(-50%);
    width: 340px;
    padding: 24px;
    border-radius: 24px;
    background: var(--glass-bg);
    backdrop-filter: blur(20px) saturate(180%);
    -webkit-backdrop-filter: blur(20px) saturate(180%);
    border: 1px solid var(--glass-border);
    box-shadow:
      inset 0 1px 0 rgba(255,255,255,0.4),
      0 8px 24px rgba(0,0,0,0.12);
    max-height: 560px;
    display: flex;
    flex-direction: column;
    z-index: 20;
  }
  .playlist-panel.dragging { user-select: none; cursor: grabbing; }
  .playlist-header {
    display: flex; align-items: center; justify-content: space-between;
    cursor: grab;
    margin-bottom: 14px;
  }
  .playlist-title {
    font-size: 16px; font-weight: 700;
    color: var(--icon-active);
    display: flex; align-items: center; gap: 8px;
  }
  .playlist-title svg { width: 18px; height: 18px; fill: #0a84ff; }
  .playlist-count {
    font-size: 12px;
    color: var(--icon-color);
    background: rgba(255,255,255,0.2);
    padding: 2px 10px;
    border-radius: 99px;
    border: 1px solid var(--glass-border);
  }
  .playlist-items {
    flex: 1;
    overflow-y: auto;
    display: flex;
    flex-direction: column;
    gap: 6px;
    padding-right: 4px;
  }
  .playlist-items::-webkit-scrollbar { width: 4px; }
  .playlist-items::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.3); border-radius: 99px; }
  .playlist-item {
    display: flex; align-items: center; gap: 10px;
    padding: 10px 12px;
    border-radius: 14px;
    cursor: pointer;
    transition: background 0.3s ease;
    border: 1px solid transparent;
  }
  .playlist-item:hover { background: rgba(255,255,255,0.12); }
  .playlist-item.active {
    background: rgba(0,122,255,0.15);
    border-color: rgba(0,122,255,0.3);
  }
  .playlist-item .pl-cover {
    width: 36px; height: 36px;
    border-radius: 10px;
    flex-shrink: 0;
    overflow: hidden;
    background: rgba(255,255,255,0.15);
  }
  .playlist-item .pl-cover img { width: 100%; height: 100%; object-fit: cover; }
  .playlist-item .pl-info { min-width: 0; flex: 1; }
  .playlist-item .pl-title {
    font-size: 13px; font-weight: 600;
    color: var(--icon-active);
    white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
  }
  .playlist-item .pl-artist {
    font-size: 11px;
    color: var(--icon-color);
    white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
  }
  .playlist-item .pl-duration {
    font-size: 11px;
    color: var(--icon-color);
    flex-shrink: 0;
  }
  .playlist-item.active .pl-title { color: #0a84ff; }

  /* 手机端隐藏播放列表 */
  @media (max-width: 900px) {
    .playlist-panel { display: none; }
  }

  /* ============================================
     新闻头条 - 播放列表下方
     ============================================ */
  .news-panel {
    position: fixed;
    right: 24px;
    top: calc(40% + 300px);
    transform: translateY(-50%);
    width: 340px;
    padding: 20px 24px;
    border-radius: 24px;
    background: var(--glass-bg);
    backdrop-filter: blur(20px) saturate(180%);
    -webkit-backdrop-filter: blur(20px) saturate(180%);
    border: 1px solid var(--glass-border);
    box-shadow:
      inset 0 1px 0 rgba(255,255,255,0.4),
      0 8px 24px rgba(0,0,0,0.12);
    max-height: 320px;
    display: flex;
    flex-direction: column;
    z-index: 20;
  }
  .news-panel.dragging { user-select: none; cursor: grabbing; }
  .news-header {
    display: flex; align-items: center; justify-content: space-between;
    cursor: grab;
    margin-bottom: 12px;
  }
  .news-title {
    font-size: 16px; font-weight: 700;
    color: var(--icon-active);
    display: flex; align-items: center; gap: 8px;
  }
  .news-title svg { width: 18px; height: 18px; fill: #ff9500; }
  .news-items {
    flex: 1;
    overflow-y: auto;
    display: flex;
    flex-direction: column;
    gap: 10px;
    padding-right: 4px;
  }
  .news-items::-webkit-scrollbar { width: 4px; }
  .news-items::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.3); border-radius: 99px; }
  .news-item {
    display: flex; gap: 12px;
    padding: 8px;
    border-radius: 14px;
    cursor: pointer;
    transition: background 0.3s ease;
    text-decoration: none;
  }
  .news-item:hover { background: rgba(255,255,255,0.12); }
  .news-item .news-img {
    width: 64px; height: 64px;
    border-radius: 12px;
    flex-shrink: 0;
    overflow: hidden;
    background: rgba(255,255,255,0.15);
  }
  .news-item .news-img img { width: 100%; height: 100%; object-fit: cover; }
  .news-item .news-text { min-width: 0; flex: 1; display: flex; flex-direction: column; gap: 4px; }
  .news-item .news-headline {
    font-size: 13px; font-weight: 600;
    color: var(--icon-active);
    line-height: 1.4;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
  }
  .news-item .news-source {
    font-size: 11px;
    color: var(--icon-color);
    opacity: 0.7;
    margin-top: auto;
  }

  /* 手机端隐藏新闻头条 */
  @media (max-width: 900px) {
    .news-panel { display: none; }
  }

  /* ============================================
     公告窗口 - 液态玻璃
     ============================================ */
  .notice-overlay {
    position: fixed; inset: 0;
    display: flex; align-items: center; justify-content: center;
    background: rgba(0,0,0,0.35);
    backdrop-filter: blur(6px);
    -webkit-backdrop-filter: blur(6px);
    z-index: 9999;
    opacity: 0; pointer-events: none;
    transition: opacity 0.4s ease;
  }
  .notice-overlay.show { opacity: 1; pointer-events: auto; }
  .notice-card {
    position: relative;
    width: 420px; max-width: 88vw;
    padding: 28px 28px 24px;
    border-radius: 28px;
    background: var(--glass-bg);
    backdrop-filter: blur(50px) saturate(200%);
    -webkit-backdrop-filter: blur(50px) saturate(200%);
    border: 1px solid var(--glass-border);
    box-shadow:
      inset 0 1px 0 rgba(255,255,255,0.5),
      inset 0 -1px 0 rgba(255,255,255,0.1),
      0 30px 60px -15px rgba(0,0,0,0.3);
    transform: scale(0.9) translateY(20px);
    opacity: 0;
    transition: transform 0.5s cubic-bezier(0.34, 1.4, 0.5, 1),
                opacity 0.4s ease;
  }
  .notice-overlay.show .notice-card {
    transform: scale(1) translateY(0);
    opacity: 1;
  }
  .notice-card::before {
    content: ''; position: absolute;
    top: 0; left: 0; right: 0; height: 50%;
    border-radius: 28px 28px 0 0;
    background: linear-gradient(180deg, rgba(255,255,255,0.12), transparent);
    pointer-events: none;
  }
  .notice-icon {
    width: 52px; height: 52px;
    border-radius: 16px;
    background: rgba(0,122,255,0.15);
    display: flex; align-items: center; justify-content: center;
    margin-bottom: 16px;
  }
  .notice-icon svg { width: 28px; height: 28px; fill: #0a84ff; }
  .notice-title {
    font-size: 20px; font-weight: 700;
    color: var(--icon-active);
    margin-bottom: 10px;
  }
  .notice-body {
    font-size: 14px; line-height: 1.7;
    color: var(--icon-color);
    margin-bottom: 24px;
  }
  .notice-actions { display: flex; gap: 12px; }
  .notice-btn {
    flex: 1; padding: 12px;
    border: none; border-radius: 16px;
    font-size: 15px; font-weight: 600;
    cursor: pointer;
    transition: transform 0.3s cubic-bezier(0.34, 1.4, 0.5, 1),
                background 0.3s ease;
  }
  .notice-btn:active { transform: scale(0.95); }
  .notice-btn-primary {
    background: #0a84ff; color: #fff;
    box-shadow: 0 8px 20px -6px rgba(10,122,255,0.5);
  }
  .notice-btn-primary:hover { background: #0066cc; }
  .notice-btn-secondary {
    background: rgba(120,120,128,0.2);
    color: var(--icon-active);
    border: 1px solid var(--glass-border);
  }
  .notice-btn-secondary:hover { background: rgba(120,120,128,0.3); }
</style>
<style>
  .search-container {
    position: relative;
    box-sizing: border-box;
    width: 600px;
    max-width: 90vw;
    padding: 14px 18px;
    border-radius: 50px;
    /* 替换为新的液态玻璃效果 */
    background: rgba(255,255,255, 0.1);
    backdrop-filter: blur(2px) url(#liquid_glass_filter);
    -webkit-backdrop-filter: blur(2px) url(#liquid_glass_filter);
    box-shadow:
      inset 0 1px 0 rgba(255,255,255,0.45),
      inset 0 -1px 0 rgba(255,255,255,0.18),
      inset 6px 6px 16px rgba(255,255,255,0.12),
      0 10px 28px rgba(0,0,0,0.35);
    transition: all 0.5s ease;
    z-index: 10;
  }

  .search-container::before {
    content: '';
    position: absolute;
    top: 1px; left: 1px; right: 1px; height: 46%;
    border-radius: 50px 50px 0 0;
    background: linear-gradient(180deg, var(--reflection-start) 0%, var(--reflection-end) 100%);
    pointer-events: none;
    z-index: 6;
    transition: background 0.5s ease;
  }

  .search-glare-container {
    position: absolute;
    inset: 0;
    border-radius: 50px;
    overflow: hidden;
    pointer-events: none;
    z-index: 5;
  }

  .search-glare {
    position: absolute;
    inset: 0;
    opacity: 0;
    transition: opacity 0.3s ease;
    background: radial-gradient(circle 120px at var(--sx, 50%) var(--sy, 50%), var(--glare-color) 0%, transparent 100%);
    mix-blend-mode: overlay;
  }

  .search-container:hover .search-glare {
    opacity: 0.3;
  }

  .search-content {
    position: relative;
    display: flex;
    align-items: center;
    gap: 12px;
    z-index: 2;
    min-width: 0;
  }

  .search-icon {
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--icon-color);
    flex-shrink: 0;
    transition: color 0.3s ease;
  }

  .search-content .search-input {
    flex: 1;
    min-width: 0;
    background: transparent;
    border: none;
    outline: none;
    height: 44px;
    line-height: 44px;
    color: var(--icon-active);
    font-family: inherit;
    font-size: 15px;
    font-weight: 500;
    transition: color 0.3s ease;
  }

  .search-content .search-input::placeholder {
    color: var(--icon-color);
    transition: color 0.3s ease;
  }

  .search-actions {
    display: flex;
    align-items: center;
    gap: 6px;
    flex-shrink: 0;
  }

  .action-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 36px;
    height: 36px;
    border-radius: 99px;
    background: var(--pill-bg);
    border: none;
    color: var(--icon-color);
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: var(--pill-shadow);
  }

  .action-btn:hover {
    color: var(--icon-active);
    transform: translateY(-2px);
  }

  .action-btn:active {
    transform: scale(0.92);
  }

  .submit-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 40px;
    height: 40px;
    border-radius: 99px;
    background: linear-gradient(135deg, var(--pill-bg), var(--glass-border));
    border: none;
    color: var(--icon-active);
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: var(--pill-shadow);
  }

  .submit-btn:hover {
    transform: translateY(-2px) scale(1.05);
  }

  .submit-btn:active {
    transform: scale(0.92);
  }

  @media (max-width: 480px) {
    .search-container {
      width: calc(100vw - 24px);
      max-width: calc(100vw - 24px);
      padding: 10px 12px;
    }

    .search-content {
      gap: 8px;
    }

    .search-icon {
      width: 18px;
      flex: 0 0 18px;
    }

    .search-content .search-input {
      height: 38px;
      line-height: 38px;
      font-size: 14px;
    }

    .search-actions {
      gap: 4px;
    }

    .action-btn {
      width: 32px;
      height: 32px;
      flex: 0 0 32px;
    }

    .submit-btn {
      width: 34px;
      height: 34px;
      flex: 0 0 34px;
    }
  }
</style>
<style>
  /* ============================================
     iOS dynamic music island - liquid glass
     ============================================ */
  .music-island {
    position: fixed;
    top: 20px;
    left: 50%;
    transform: translateX(-50%);
    z-index: 100;
    cursor: pointer;
    width: min(360px, calc(100vw - 32px));
    -webkit-tap-highlight-color: transparent;
  }

  .music-container {
    position: relative;
    display: flex;
    align-items: center;
    min-height: 54px;
    padding: 8px 14px 8px 9px;
    border-radius: 999px;
    gap: 12px;
    /* 替换为新的液态玻璃效果 */
    background: rgba(255,255,255, 0.1);
    backdrop-filter: blur(2px) url(#liquid_glass_filter);
    -webkit-backdrop-filter: blur(2px) url(#liquid_glass_filter);
    box-shadow:
      inset 0 1px 0 rgba(255,255,255,0.45),
      inset 0 -1px 0 rgba(255,255,255,0.18),
      inset 6px 6px 16px rgba(255,255,255,0.12),
      0 10px 28px rgba(0,0,0,0.35);
    overflow: hidden;
    transition: min-height 0.55s cubic-bezier(0.34, 1.2, 0.64, 1),
      padding 0.55s cubic-bezier(0.34, 1.2, 0.64, 1),
      border-radius 0.55s cubic-bezier(0.34, 1.2, 0.64, 1),
      box-shadow 0.5s ease,
      transform 0.35s ease;
  }

  .music-container:hover {
    transform: translateY(-2px);
  }

  .music-container::before {
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
    transition: background 0.5s ease;
  }

  .music-island.expanded .music-container {
    min-height: 238px;
    padding: 18px;
    border-radius: 32px;
  }

  .music-compact {
    position: relative;
    display: flex;
    align-items: center;
    gap: 10px;
    width: 100%;
    z-index: 3;
    transition: opacity 0.25s ease, transform 0.35s ease;
  }

  .music-cover {
    width: 38px;
    height: 38px;
    border-radius: 50%;
    flex-shrink: 0;
    background: rgba(255,255,255,0.16);
    box-shadow:
      inset 0 0 0 1px rgba(255,255,255,0.45),
      0 10px 18px -10px var(--glass-shadow);
    overflow: hidden;
  }

  .music-cover img {
    width: 100%;
    height: 100%;
    display: block;
    object-fit: contain;
  }

  .music-brief {
    display: flex;
    flex-direction: column;
    gap: 2px;
    min-width: 0;
    flex: 1;
  }

  .music-title {
    font-size: 13px;
    font-weight: 700;
    color: var(--icon-active);
    line-height: 1.2;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }

  .music-artist {
    font-size: 11px;
    color: var(--icon-color);
    line-height: 1.2;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }

  .music-equalizer {
    display: flex;
    align-items: end;
    gap: 3px;
    height: 18px;
    color: var(--icon-color);
    transition: color 0.3s ease;
  }

  .music-equalizer span {
    width: 3px;
    height: 8px;
    border-radius: 99px;
    background: currentColor;
    opacity: 0.72;
    transition: height 0.16s ease, opacity 0.16s ease;
  }

  .music-island.expanded .music-equalizer {
    color: #1e9cff;
  }

  .music-panel {
    position: absolute;
    inset: 18px;
    z-index: 3;
    display: flex;
    flex-direction: column;
    align-items: center;
    opacity: 0;
    transform: translateY(18px) scale(0.96);
    pointer-events: none;
    transition: opacity 0.3s ease, transform 0.45s cubic-bezier(0.34, 1.2, 0.64, 1);
  }

  .music-panel > .music-equalizer {
    position: absolute;
    top: 4px;
    right: 2px;
    height: 22px;
    color: #1e9cff;
  }

  .music-island.expanded .music-compact {
    opacity: 0;
    transform: translateY(-10px) scale(0.96);
    pointer-events: none;
  }

  .music-island.expanded .music-panel {
    opacity: 1;
    transform: translateY(0) scale(1);
    pointer-events: auto;
  }

  .music-panel-head {
    display: grid;
    grid-template-columns: 74px minmax(0, 1fr);
    gap: 14px;
    width: 100%;
    align-items: center;
    padding-right: 34px;
    box-sizing: border-box;
  }

  .music-panel-cover {
    width: 74px;
    height: 74px;
    border-radius: 22px;
  }

  .music-meta {
    min-width: 0;
  }

  .music-panel-title {
    font-size: 18px;
    font-weight: 800;
    line-height: 1.2;
    color: var(--icon-active);
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }

  .music-panel-artist {
    margin-top: 5px;
    font-size: 13px;
    color: var(--icon-color);
  }

  .music-progress {
    width: 100%;
    margin-top: 22px;
  }

  .music-progress-track {
    position: relative;
    height: 6px;
    border-radius: 99px;
    background: rgba(127, 127, 127, 0.25);
    box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.15);
    cursor: pointer;
    touch-action: none;
    transition: height 0.2s ease;
    overflow: hidden;
  }

  .music-progress-track::before {
    content: '';
    position: absolute;
    top: -6px;
    bottom: -6px;
    left: 0;
    right: 0;
  }

  .music-progress-track:hover {
    height: 8px;
  }

  .music-progress-bar {
    width: 0%;
    height: 100%;
    border-radius: inherit;
    background: #1e9cff;
    box-shadow: 0 0 8px rgba(30, 156, 255, 0.6);
    pointer-events: none;
    transition: width 0.1s linear;
  }

  .music-time {
    display: flex;
    justify-content: space-between;
    margin-top: 7px;
    font-size: 11px;
    color: var(--icon-color);
    opacity: 0.74;
    user-select: none;
  }

  .music-controls {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 22px;
    width: 100%;
    margin-top: 20px;
  }

  .music-control {
    width: 38px;
    height: 38px;
    border: none;
    border-radius: 50%;
    background: rgba(255,255,255,0.13);
    color: var(--icon-active);
    display: grid;
    place-items: center;
    cursor: pointer;
    box-shadow:
      inset 0 1px 2px var(--glass-highlight),
      inset 0 0 0 1px var(--glass-border);
    transition: transform 0.2s ease, background 0.3s ease;
  }

  .music-control:active {
    transform: scale(0.9);
  }

  .music-control.play {
    width: 54px;
    height: 54px;
    background: var(--pill-bg);
    box-shadow: var(--pill-shadow);
  }

  @media (max-width: 480px) {
    .music-island {
      top: 14px;
      width: min(330px, calc(100vw - 24px));
    }

    .music-island.expanded .music-container {
      min-height: 226px;
      border-radius: 28px;
      padding: 16px;
    }

    .music-panel {
      inset: 16px;
    }
  }
</style>
<style>
        :root {
            --bg-color: #e5e5ea;
            --blob-1: #ff2a5f;
            --blob-2: #007aff;
            --blob-3: #ff9500;
            --blob-opacity: 0.7;
            
            --glass-bg: rgba(255, 255, 255, 0.1);
            --glass-border: rgba(255, 255, 255, 0.4);
            --glass-shadow: rgba(0, 0, 0, 0.1);
            --glass-highlight: rgba(255, 255, 255, 0.8);
            --glass-caustic: rgba(255, 255, 255, 0.4);
            --reflection-start: rgba(255, 255, 255, 0.6);
            --reflection-end: rgba(255, 255, 255, 0.0);
            --glare-color: rgba(255, 255, 255, 0.5);
            
            --pill-bg: rgba(255, 255, 255, 0.7);
            --pill-shadow: 0 4px 12px rgba(0,0,0,0.08), 0 1px 2px rgba(0,0,0,0.05), inset 0 1px 1px rgba(255,255,255,0.8);
            --icon-color: rgba(0, 0, 0, 0.5);
            --icon-active: rgba(0, 0, 0, 0.95);
            --divider: rgba(0, 0, 0, 0.15);
        }

        [data-theme="dark"] {
            --bg-color: #000000;
            --blob-1: #bf5af2;
            --blob-2: #0a84ff;
            --blob-3: #ff375f;
            --blob-opacity: 0.5;
            
            --glass-bg: rgba(30, 30, 35, 0.45);
            --glass-border: rgba(255, 255, 255, 0.15);
            --glass-shadow: rgba(0, 0, 0, 0.8);
            --glass-highlight: rgba(255, 255, 255, 0.25);
            --glass-caustic: rgba(255, 255, 255, 0.05);
            --reflection-start: rgba(255, 255, 255, 0.15);
            --reflection-end: rgba(255, 255, 255, 0.0);
            --glare-color: rgba(255, 255, 255, 0.15);
            
            --pill-bg: rgba(60, 60, 65, 0.8);
            --pill-shadow: 0 4px 12px rgba(0,0,0,0.4), 0 1px 2px rgba(0,0,0,0.2), inset 0 1px 1px rgba(255,255,255,0.2);
            --icon-color: rgba(255, 255, 255, 0.5);
            --icon-active: #ffffff;
            --divider: rgba(255, 255, 255, 0.15);
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            font-family: -apple-system, BlinkMacSystemFont, "SF Pro Display", "SF Pro Text", "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            background: url('back.png') no-repeat;
            background-size: cover;
            -webkit-font-smoothing: antialiased;
            overflow: hidden;
        }

        .bg-mesh {
            position: absolute;
            inset: 0;
            z-index: -1;
            background: var(--bg-color);
            transition: background 0.8s ease;
            overflow: hidden;
        }

        .blob {
            position: absolute;
            border-radius: 50%;
            filter: blur(90px);
            opacity: var(--blob-opacity);
            animation: float 20s infinite alternate cubic-bezier(0.45, 0.05, 0.55, 0.95);
            will-change: transform;
            transition: background 0.8s ease, opacity 0.8s ease;
        }

        .blob-1 { width: 50vw; height: 50vw; top: -10%; left: -10%; background: var(--blob-1); animation-delay: 0s; }
        .blob-2 { width: 45vw; height: 45vw; bottom: -10%; right: -10%; background: var(--blob-2); animation-delay: -5s; }
        .blob-3 { width: 35vw; height: 35vw; top: 30%; left: 40%; background: var(--blob-3); animation-delay: -10s; }

        @keyframes float {
            0% { transform: translate(0, 0) scale(1) rotate(0deg); }
            33% { transform: translate(5%, 10%) scale(1.05) rotate(5deg); }
            66% { transform: translate(-5%, 5%) scale(0.95) rotate(-5deg); }
            100% { transform: translate(0, -10%) scale(1.1) rotate(0deg); }
        }

        .liquid-nav {
            position: absolute;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 8px;
            border-radius: 99px;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%);
            /* 替换为新的液态玻璃效果 */
            background: rgba(255,255,255, 0.1);
            backdrop-filter: blur(2px) url(#liquid_glass_filter);
            -webkit-backdrop-filter: blur(2px) url(#liquid_glass_filter);
            box-shadow:
              inset 0 1px 0 rgba(255,255,255,0.45),
              inset 0 -1px 0 rgba(255,255,255,0.18),
              inset 6px 6px 16px rgba(255,255,255,0.12),
              0 10px 28px rgba(0,0,0,0.35);
            transition: all 0.5s ease;
            z-index: 10;
        }

        /* 浏览器不兼容 backdrop-filter 时的 Fallback */
        @supports not (backdrop-filter: blur(2px)) {
          .engine-selector,
          .search-container,
          .music-container,
          .liquid-nav {
            background: linear-gradient(135deg, rgba(255,255,255,0.10), rgba(255,255,255,0.04));
          }
        }

        .liquid-nav::before {
            content: '';
            position: absolute;
            top: 1px; left: 1px; right: 1px; height: 46%;
            border-radius: 99px 99px 24px 24px / 99px 99px 12px 12px;
            background: linear-gradient(180deg, var(--reflection-start) 0%, var(--reflection-end) 100%);
            pointer-events: none;
            z-index: 6;
            transition: background 0.5s ease;
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
            transition: opacity 0.3s ease;
            background: radial-gradient(circle 90px at var(--x, 50%) var(--y, 50%), var(--glare-color) 0%, transparent 100%);
            mix-blend-mode: overlay;
        }
        
        .liquid-nav:hover .liquid-glare {
            opacity: 0.3;
        }

        .nav-items {
            position: relative;
            display: flex;
            gap: 4px;
            z-index: 3;
        }

        .active-pill {
            position: absolute;
            top: 0; left: 0;
            height: 44px;
            background: var(--pill-bg);
            border-radius: 99px;
            box-shadow: var(--pill-shadow);
            transition: transform 0.5s cubic-bezier(0.34, 1.2, 0.64, 1), 
                        width 0.5s cubic-bezier(0.34, 1.2, 0.64, 1),
                        background 0.5s ease, box-shadow 0.5s ease;
            z-index: 1;
        }

        .nav-btn {
            position: relative;
            background: transparent;
            border: none;
            padding: 0 20px;
            height: 44px;
            border-radius: 99px;
            font-family: inherit;
            font-size: 15px;
            font-weight: 600;
            color: var(--icon-color);
            cursor: pointer;
            -webkit-tap-highlight-color: transparent;
            transition: color 0.3s ease;
            outline: none;
            z-index: 2;
        }

        .btn-content {
            display: flex;
            align-items: center;
            gap: 8px;
            pointer-events: none;
            transition: transform 0.2s cubic-bezier(0.32, 0.72, 0, 1);
        }

        .nav-btn:active .btn-content {
            transform: scale(0.92);
        }

        .nav-btn.active {
            color: var(--icon-active);
        }

        .divider {
            width: 1px;
            height: 24px;
            background: var(--divider);
            margin: 0 4px;
            z-index: 3;
            transition: background 0.5s ease;
        }

        .theme-btn {
            position: relative;
            background: transparent;
            border: none;
            width: 44px;
            height: 44px;
            border-radius: 50%;
            color: var(--icon-color);
            cursor: pointer;
            -webkit-tap-highlight-color: transparent;
            z-index: 3;
            outline: none;
            margin-left: 2px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: color 0.3s ease;
        }

        .theme-btn:hover, .theme-btn:active {
            color: var(--icon-active);
        }

        .theme-icon-wrapper {
            position: relative;
            width: 20px;
            height: 20px;
            pointer-events: none;
            transition: transform 0.2s cubic-bezier(0.32, 0.72, 0, 1);
        }

        .theme-btn:active .theme-icon-wrapper {
            transform: scale(0.8);
        }

        .theme-icon-wrapper svg {
            position: absolute;
            top: 0; left: 0;
            transition: transform 0.5s cubic-bezier(0.34, 1.2, 0.64, 1), opacity 0.4s ease;
            stroke-width: 2.2;
        }

        .sun { opacity: 1; transform: rotate(0deg) scale(1); }
        .moon { opacity: 0; transform: rotate(-90deg) scale(0); }

        [data-theme="dark"] .sun { opacity: 0; transform: rotate(90deg) scale(0); }
        [data-theme="dark"] .moon { opacity: 1; transform: rotate(0deg) scale(1); }
    </style>

<body>

<!-- ===== SVG 液态玻璃折射滤镜（参考 macOS Liquid Glass）===== -->
<svg style="display:none; position:absolute; width:0; height:0;">
  <filter id="liquid_glass_filter" x="-20%" y="-20%" width="140%" height="140%" filterUnits="objectBoundingBox">
    <!-- 低频分形噪声：产生大尺度平滑扭曲场 -->
    <feTurbulence type="fractalNoise" baseFrequency="0.01 0.01" numOctaves="1" seed="5" result="turbulence" />
    <!-- 缓和噪声边缘，避免锯齿，形成连续液态扭曲 -->
    <feGaussianBlur in="turbulence" stdDeviation="3" result="softMap" />
    <!-- 高强度位移映射：scale 越大折射越明显 -->
    <feDisplacementMap in="SourceGraphic" in2="softMap" scale="100" xChannelSelector="R" yChannelSelector="G" />
  </filter>
</svg>
  
<!-- ===== 顶部灵动岛音乐播放器 ===== -->
<div class="music-island" id="music-island" role="button" tabindex="0" aria-expanded="false" aria-label="音乐播放器">
    <div class="music-container" id="music-container">
        <div class="music-compact" aria-hidden="false">
            <div class="music-cover">
                <img src="https://file.kxlove.top/view.php/a2d2a15fdb941e06e02634ab286fd71d.png" alt="专辑封面">
            </div>
            <div class="music-brief">
                <div class="music-title">Liquid Dreams</div>
                <div class="music-artist">Glass FM</div>
            </div>
            <div class="music-equalizer" aria-hidden="true">
                <span></span><span></span><span></span><span></span><span></span>
            </div>
        </div>

        <div class="music-panel" aria-hidden="true">
            <div class="music-equalizer" aria-hidden="true">
                <span></span><span></span><span></span><span></span><span></span>
            </div>

            <div class="music-panel-head">
                <div class="music-cover music-panel-cover">
                    <img src="https://file.kxlove.top/view.php/a2d2a15fdb941e06e02634ab286fd71d.png" alt="专辑封面">
                </div>
                <div class="music-meta">
                    <div class="music-panel-title">Liquid Dreams</div>
                    <div class="music-panel-artist">Glass FM</div>
                </div>
            </div>

            <div class="music-progress">
                <div class="music-progress-track" id="music-progress-track">
                    <div class="music-progress-bar" id="music-progress-bar"></div>
                </div>
                <div class="music-time"><span id="music-current-time">0:00</span><span id="music-duration">0:00</span></div>
            </div>

            <div class="music-controls">
                <button class="music-control" type="button" aria-label="Previous">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M6 6h2v12H6zM9 12l9-6v12z"/></svg>
                </button>
                <button class="music-control play" type="button" aria-label="Play or pause" aria-pressed="false">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M8 5v14l11-7z"/></svg>
                </button>
                <button class="music-control" type="button" aria-label="Next">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M16 6h2v12h-2zM6 18l9-6-9-6z"/></svg>
                </button>
            </div>
        </div>
    </div>
    <audio id="music-audio" preload="metadata" src="https://file.kxlove.top/view.php/41110a6125f7624b2376677cef0d82aa.aac"></audio>
</div>

<!-- ===== 背景气泡层 ===== -->
<div class="bg-mesh">
    <div class="blob blob-1"></div>
    <div class="blob blob-2"></div>
    <div class="blob blob-3"></div>
</div>

<!-- ===== 搜索区域容器 ===== -->
<div class="search-wrapper">
    <!-- ===== 时钟 ===== -->
    <div class="clock-panel">
        <span class="clock-time" id="clock-hm">00:00</span>
        <span class="clock-seconds" id="clock-s">00</span>
        <div class="clock-date">
            <div id="clock-date">2026.01.01</div>
            <div class="clock-week" id="clock-week">周一</div>
        </div>
    </div>

    <div class="engine-selector" id="engine-selector">
        <div class="engine-items">
            <div class="engine-pill" id="engine-pill"></div>

            <button class="engine-btn active" data-engine="bing" data-url="https://www.bing.com/search?q=">
                <div class="btn-content">
                    <svg width="14" height="14" viewBox="0 0 1024 1024" xmlns="http://www.w3.org/2000/svg">
                        <path d="M340.5824 70.109867L102.536533 0.682667v851.217066L340.650667 643.345067V70.109867zM102.536533 851.7632l238.045867 171.6224 580.881067-340.923733V411.784533L102.536533 851.831467z" fill="#1E9CFF"></path>
                        <path d="M409.463467 255.3856l113.732266 238.933333 138.8544 56.866134 259.413334-139.400534-506.0608-156.330666z" fill="#1E9CFF"></path>
                    </svg>
                    <span>Bing</span>
                </div>
            </button>
            <style>
                @media (max-width: 768px) {
                  .engine-btn .btn-content {
                    justify-content: center;
                  }
                  .engine-btn .btn-content span {
                    display: none !important;
                  }
                }
            </style>

            <button class="engine-btn" data-engine="google" data-url="https://www.google.com/search?q=">
                <div class="btn-content">
                    <svg width="14" height="14" viewBox="0 0 1024 1024" xmlns="http://www.w3.org/2000/svg">
                        <path d="M222.087529 515.915294c0-32.527059 5.481412-63.698824 15.179295-92.973176l-170.285177-128.90353A496.941176 496.941176 0 0 0 15.058824 515.945412c0 79.721412 18.672941 154.955294 51.802352 221.726117l170.255059-129.114353a293.767529 293.767529 0 0 1-15.028706-92.611764" fill="#FBBC05"></path>
                        <path d="M521.246118 219.979294a294.460235 294.460235 0 0 1 186.337882 65.957647l147.245176-145.648941C765.108706 62.885647 650.089412 15.058824 521.246118 15.058824 321.204706 15.058824 149.293176 128.421647 66.981647 294.038588l170.405647 128.90353c39.243294-118.061176 151.100235-202.992941 283.919059-202.992942" fill="#EA4335"></path>
                        <path d="M523.414588 805.315765c-133.511529 0-245.940706-84.359529-285.394823-201.728l-171.188706 128.12047C149.564235 896.301176 322.349176 1008.941176 523.444706 1008.941176c124.024471 0 242.447059-43.128471 331.38447-124.054588l-162.514823-123.090823c-45.808941 28.310588-103.574588 43.550118-168.929882 43.550117" fill="#34A853"></path>
                        <path d="M1008.941176 511.216941c0-29.394824-4.638118-61.108706-11.565176-90.503529H523.354353v192.301176h272.835765c-13.613176 65.596235-50.718118 115.983059-103.875765 148.781177l162.484706 123.090823c93.394824-84.901647 154.142118-211.425882 154.142117-373.669647" fill="#4285F4"></path>
                    </svg>
                    <span>Google</span>
                </div>
            </button>

            <button class="engine-btn" data-engine="baidu" data-url="https://www.baidu.com/s?wd=">
                <div class="btn-content">
                    <svg width="14" height="14" viewBox="0 0 1024 1024" xmlns="http://www.w3.org/2000/svg">
                        <path d="M345.706 297.423c15.35 12.792 35.817 20.467 53.726 17.908 20.467 0 38.375-10.233 53.725-23.025 17.91-15.35 30.7-35.817 40.934-58.842C512 189.97 512 136.245 496.65 90.194c-10.234-30.7-28.142-58.842-53.726-76.75C427.574 3.21 404.548-1.906 384.081 0.652c-12.791 2.558-25.583 7.675-38.375 15.35-23.025 15.35-38.376 40.934-48.61 66.518-12.791 46.05-15.35 92.101-2.557 138.152 10.233 28.142 25.583 56.284 51.167 76.75z m255.837 2.558c17.909 15.35 40.934 25.584 63.96 25.584 20.466 2.558 38.375-2.558 56.283-12.792 17.909-10.233 33.26-25.584 43.493-43.492 12.792-20.467 23.025-40.934 30.7-63.96 5.117-17.908 7.675-38.375 5.117-58.842-2.559-28.142-15.35-53.726-33.259-76.751-12.792-15.35-28.142-30.7-46.05-38.376-12.792-5.116-28.143-10.233-40.935-7.675-17.908 2.559-33.258 12.792-46.05 23.026-17.909 15.35-33.259 33.258-43.493 53.725-10.233 17.909-20.466 38.376-23.025 61.401-2.558 25.584-2.558 51.168 2.559 74.193 5.116 23.025 12.791 46.05 30.7 63.96zM245.929 509.768c17.91-15.35 28.143-35.818 35.818-56.285 10.233-33.258 10.233-66.517 7.675-99.776 0-12.792-5.117-25.584-10.234-38.376-12.792-28.142-35.817-53.725-63.959-69.076-23.025-10.233-46.05-15.35-66.518-10.233-25.583 2.558-46.05 20.467-61.4 40.934-20.467 28.142-30.7 63.96-35.818 97.218-2.558 20.467 0 40.934 5.117 61.4 7.675 30.701 23.025 58.843 46.05 79.31 17.91 15.35 40.935 23.026 63.96 23.026 28.142 0 56.284-7.675 79.31-28.142z m736.811-76.752c-2.558-20.467-7.675-38.375-17.908-56.284-10.234-20.467-28.143-40.934-48.61-51.167-23.025-12.792-51.167-15.35-76.75-12.792-12.792 2.558-28.143 5.117-40.935 12.792-17.908 10.233-30.7 28.142-40.933 48.609-10.234 25.584-15.35 53.726-15.35 81.868 0 25.583 0 53.726 7.674 79.31 5.117 17.908 15.35 38.375 33.26 48.608 17.908 15.35 40.933 20.467 63.959 23.026 17.908 2.558 38.375 2.558 56.284-2.559 17.908-5.116 35.817-15.35 46.05-30.7 12.792-15.35 20.467-35.817 23.026-53.726 12.792-30.7 10.233-58.842 10.233-86.985zM911.106 819.33c-2.559-35.817-20.467-71.634-46.05-99.776-5.118-5.117-10.234-10.234-17.91-15.35-33.258-28.142-66.517-58.843-99.776-89.543-33.259-33.26-63.96-69.076-92.101-107.452-20.467-33.259-48.61-63.96-86.985-81.868-23.025-10.233-51.167-15.35-76.751-12.792-46.05 5.117-86.985 30.7-115.127 66.518-7.675 7.675-12.791 17.909-17.908 28.142-20.467 30.7-46.05 61.401-74.193 86.985-15.35 15.35-30.7 28.142-46.05 40.934-7.676 7.675-17.91 15.35-25.584 23.025-30.7 23.025-61.401 53.726-79.31 86.985-12.792 23.025-20.467 48.609-23.025 76.75 0 23.026 2.558 46.051 10.233 66.518 7.675 23.026 17.909 46.051 33.26 63.96 25.583 30.7 63.958 51.167 102.334 53.725 48.609 2.559 97.218 0 143.269-7.675 20.467-2.558 40.934-10.233 63.959-12.792 46.05-5.116 92.101-2.558 135.594 10.234 35.817 12.792 74.192 17.909 112.568 20.467 38.375 2.558 79.31-2.558 115.127-23.025 25.583-12.792 46.05-35.818 58.842-61.401 20.467-33.26 30.7-71.635 25.584-112.569zM481.3 924.224H363.615c-12.792 0-25.584 0-38.376-2.559-25.584-5.117-48.61-20.467-63.96-43.492-12.791-15.35-20.466-33.259-23.025-53.726a246.563 246.563 0 0 1 0-61.4c5.117-23.026 17.909-43.493 33.26-58.843 12.791-12.792 30.7-23.026 48.608-30.7 7.675-2.56 15.35-5.117 23.026-5.117h69.076v-97.219h66.517c2.559 120.244 2.559 237.929 2.559 353.056z m263.512 0H583.634c-17.908-2.559-33.258-7.676-46.05-17.909-15.35-12.792-23.026-33.259-23.026-51.167v-173.97h66.518v161.178c0 7.675 2.558 12.792 7.675 17.908 5.117 5.117 12.792 7.675 20.467 7.675h69.076V678.62h66.518v245.604z" fill="#1E9CFF"></path>
                        <path d="M340.59 734.904c-12.793 5.117-25.585 15.35-33.26 30.7-5.116 12.792-7.675 25.584-7.675 38.376 0 15.35 5.117 30.7 12.792 43.492 10.234 15.35 28.142 25.584 46.05 23.026h53.727V732.346H353.38c-2.558-2.559-7.675 0-12.792 2.558z" fill="#1E9CFF"></path>
                    </svg>
                    <span>百度</span>
                </div>
            </button>

            <button class="engine-btn" data-engine="360" data-url="https://www.so.com/s?q=">
                <div class="btn-content">
                    <svg width="14" height="14" viewBox="0 0 1155 1024" xmlns="http://www.w3.org/2000/svg">
                        <path d="M980.930724 675.810699c-16.652996-87.217834-67.657577-118.672359-153.013743-94.358475-16.280662 10.343729-29.883584 23.094874-40.803665 38.253436 48.87769-173.410475-5.528897-303.467056-163.214659-390.185045-180.459308-57.176135-313.922996-1.922873-400.385962 165.764888-29.72547 72.803939-30.577246 145.908805-2.550229 219.319699-52.616326-55.273665-107.874689-58.67567-165.764888-10.200916-15.260571 19.432745-22.911258 41.53303-22.952062 66.305955a574.928738 574.928738 0 0 1-30.602748-102.009162v-127.511453C34.856951 253.314252 138.564566 119.850565 312.770712 40.803665 536.599216-37.758691 729.569948 8.992108 891.672707 181.066263c116.193536 149.484226 145.944508 314.397338 89.258017 494.744436z" fill="#10B163"></path>
                        <path d="M220.962466 614.605202c76.445666 156.987 200.555113 217.345821 372.333442 181.066263 88.039007-32.780644 152.64141-91.435912 193.817408-175.965805 10.920081-15.158561 24.523003-27.909707 40.803665-38.253436 85.356166-24.313884 136.360748 7.140641 153.013743 94.358475-85.203153 213.581683-243.317354 325.791762-474.342604 336.630235-207.700855-4.774029-359.862822-98.280727-456.491-280.525196-10.486542-19.218526-16.433676-39.620359-17.851604-61.205497 0.040804-24.772925 7.691491-46.87321 22.952062-66.305955 57.8902-48.474754 113.148563-45.072748 165.764888 10.200916z" fill="#FE9932"></path>
                        <path d="M1011.533473 808.42261c98.275627-4.605714 139.931068 41.298409 124.961224 137.712369-35.499188 66.076435-87.355546 82.224485-155.563973 48.454352-54.294377-50.984179-57.696382-104.538989-10.200916-160.66443 12.383912-11.24651 25.986834-19.748974 40.803665-25.502291z" fill="#FF9932"></path>
                    </svg>
                    <span>360</span>
                </div>
            </button>

            <button class="engine-btn" data-engine="sogou" data-url="https://www.sogou.com/web?query=">
                <div class="btn-content">
                    <svg width="14" height="14" viewBox="0 0 1024 1024" xmlns="http://www.w3.org/2000/svg">
                        <path d="M512 917.5c-225 0-407.4-182.4-407.4-407.3 0-225 182.4-407.4 407.4-407.4s407.4 182.4 407.4 407.4c0 99.9-36 191.3-95.7 262.2l32.4 23.3c64.4-77.5 103.1-177 103.1-285.5C959.1 263.2 758.9 63 512 63 265 63 64.9 263.2 64.9 510.1c0 247 200.1 447.2 447.1 447.2 58.6 0.1 116.6-11.4 170.7-33.8l-20.2-34.8c-47.8 19-98.9 28.8-150.5 28.8z m188.1 8.7l20.2 34.8c67.3-27.9 126.8-71.7 173.4-127.8l-32.4-23.3c-43.4 51.4-98.7 91.4-161.2 116.3z" fill="#FA4D23"></path>
                        <path d="M503.5 678.9s-162.7 1.2-251.3-81.3l-7 115.3s535.5 194.2 540.3-117.7c0 0 11.4-74.1-182.5-125 0 0-76.5-18.6-162.7-68 0 0-47.4-54.6 88-43.7 160.6 12.9 242.2 54.6 242.2 54.6v-102s-363.6-137.2-506.8 19.4c0 0-47.1 71.6 1.5 116.5 48.6 44.9 147.6 102 280 136 0 0 170.7 77.7-41.7 95.9z" fill="#FA4D23"></path>
                    </svg>
                    <span>搜狗</span>
                </div>
            </button>

            <button class="engine-btn" data-engine="quark" data-url="https://quark.sm.cn/s?q=">
                <div class="btn-content">
                    <svg width="14" height="14" viewBox="0 0 1024 1024" xmlns="http://www.w3.org/2000/svg">
                        <path d="M469.135 976.134c-110.259-9.764-215.516-59.535-290.768-137.168-75.49-78.11-113.831-154.553-132.168-263.859-4.763-28.339-5.477-95.97-1.19-123.833 10.24-68.822 33.1-134.072 64.297-184.32 88.35-142.407 236.71-226.47 399.598-226.47 69.537 0 132.168 12.621 192.417 39.055 52.629 23.1 110.497 64.297 149.313 106.448 60.726 65.965 91.922 122.166 114.784 206.943 18.098 66.917 18.575 160.983 1.429 227.423-19.29 73.586-45.485 126.214-92.637 184.559-40.96 50.961-84.063 86.92-140.74 117.402-59.535 32.15-114.545 48.105-184.082 53.82-34.054 2.858-47.152 2.858-80.253 0z m84.063-238.616c11.669-5 20.718-19.051 20.718-32.625 0-23.338 4.525-49.771 10.478-61.44 12.146-23.814 28.339-32.149 77.396-39.77 19.05-2.857 38.578-6.905 43.341-8.81 13.574-5.954 24.767-17.385 32.149-33.34 6.668-14.526 6.906-15.955 6.906-53.105-0.238-41.198-1.667-50.248-15.955-87.16-21.195-55.486-76.92-110.734-132.168-130.738-11.43-4.048-33.577-9.525-49.295-12.383-26.195-4.287-31.196-4.525-53.82-1.905-44.77 5.239-72.394 14.05-103.352 32.625-19.527 11.907-20.956 13.098-44.532 36.435-34.53 34.054-52.39 67.156-63.345 116.689-19.051 86.92 15.48 178.604 87.874 233.853 30.243 23.1 74.537 41.674 106.686 44.77 27.624 2.858 66.68 1.19 76.92-3.096z" fill="#3A25DD"></path>
                    </svg>
                    <span>夸克</span>
                </div>
            </button>
        </div>
    </div>

    <!-- ===== 搜索框 ===== -->
    <div class="search-container" id="search-container">
        <div class="search-glare-container">
            <div class="search-glare" id="search-glare"></div>
        </div>
        
        <div class="search-content">
            <div class="search-icon">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="11" cy="11" r="8"></circle>
                    <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                </svg>
            </div>
            
            <input type="text" class="search-input" id="search-input" placeholder="搜索任何内容...">
            
            <div class="search-actions">
                <button class="action-btn" title="附件">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21.44 11.05l-9.19 9.19a6 6 0 01-8.49-8.49l9.19-9.19a4 4 0 015.66 5.66l-9.2 9.19a2 2 0 01-2.83-2.83l8.49-8.48"></path>
                    </svg>
                </button>
                
                <button class="submit-btn" id="search-submit" title="搜索">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="22" y1="2" x2="11" y2="13"></line>
                        <polygon points="22 2 15 22 11 13 2 9 22 2"></polygon>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- ===== 网站推荐卡片（由 JS 动态渲染）===== -->
    <div class="site-cards" id="site-cards"></div>

    <!-- ===== 添加推荐表单 ===== -->
    <div class="add-form" id="add-form">
        <div class="add-form-header">
            <div class="add-form-title">
                <svg viewBox="0 0 24 24"><path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6z"/></svg>
                添加推荐
            </div>
            <span class="add-form-hint">填写站点信息</span>
        </div>
        <textarea class="add-form-textarea" id="add-form-textarea" placeholder="请输入站点信息，例如：&#10;名称：示例站点&#10;网址：https://example.com&#10;简介：一句话描述该站点"></textarea>
        <div class="add-form-actions">
            <button class="add-form-btn add-form-btn-cancel" id="add-form-cancel" type="button">取消</button>
            <button class="add-form-btn add-form-btn-submit" id="add-form-submit" type="button">提交</button>
        </div>
    </div>
</div>

<!-- ===== 开发进度表（固定浮动左侧）===== -->
<div class="dev-panel" id="dev-panel">
    <div class="dev-header">
        <div class="dev-title">
            <svg viewBox="0 0 24 24"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
            开发进度
        </div>
        <span class="dev-date">2026.07.31</span>
    </div>
    <div class="dev-items">
        <!-- 由后台 API 数据驱动，无数据时显示占位提示 -->
        <div class="dev-empty" style="padding:20px;text-align:center;color:var(--icon-color);opacity:0.5;font-size:12px">暂无进度数据，请在后台添加</div>
    </div>
</div>

<!-- ===== 每日一言（开发进度下方）===== -->
<div class="quote-panel" id="quote-panel">
    <div class="quote-header">
        <div class="quote-title">
            <svg viewBox="0 0 24 24"><path d="M14 17H7v-2h7v2zm0-4H7v-2h7v2zm0-4H7V7h7v2zm10 10H11v-2h13v2zm0-4H11v-2h13v2zm0-4H11V7h13v2z"/></svg>
            每日一言
        </div>
        <span class="quote-date" id="quote-date">加载中...</span>
    </div>
    <div class="quote-content" id="quote-content">
        加载中...
    </div>
    <div class="quote-author" id="quote-author">— 未知</div>
</div>

<!-- ===== 播放列表（固定浮动右侧）===== -->
<div class="playlist-panel" id="playlist-panel">
    <div class="playlist-header">
        <div class="playlist-title">
            <svg viewBox="0 0 24 24"><path d="M15 6H3v2h12V6zm0 4H3v2h12v-2zM3 16h8v-2H3v2zM17 6v8.18c-.31-.11-.65-.18-1-.18-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3V8h3V6h-5z"/></svg>
            播放列表
        </div>
        <span class="playlist-count" id="playlist-count">5 首</span>
    </div>
    <div class="playlist-items" id="playlist-items">
        <div class="playlist-item active" data-index="0" data-src="https://file.kxlove.top/view.php/41110a6125f7624b2376677cef0d82aa.aac" data-cover="https://file.kxlove.top/view.php/a2d2a15fdb941e06e02634ab286fd71d.png">
            <div class="pl-cover"><img src="https://file.kxlove.top/view.php/a2d2a15fdb941e06e02634ab286fd71d.png" alt="封面"></div>
            <div class="pl-info">
                <div class="pl-title">Liquid Dreams</div>
                <div class="pl-artist">Glass FM</div>
            </div>
            <span class="pl-duration">3:42</span>
        </div>
        <div class="playlist-item" data-index="1" data-src="https://file.kxlove.top/view.php/41110a6125f7624b2376677cef0d82aa.aac" data-cover="https://file.kxlove.top/view.php/a2d2a15fdb941e06e02634ab286fd71d.png">
            <div class="pl-cover"><img src="https://file.kxlove.top/view.php/a2d2a15fdb941e06e02634ab286fd71d.png" alt="封面"></div>
            <div class="pl-info">
                <div class="pl-title">Crystal Clear</div>
                <div class="pl-artist">Aurora Sound</div>
            </div>
            <span class="pl-duration">4:15</span>
        </div>
        <div class="playlist-item" data-index="2" data-src="https://file.kxlove.top/view.php/41110a6125f7624b2376677cef0d82aa.aac" data-cover="https://file.kxlove.top/view.php/a2d2a15fdb941e06e02634ab286fd71d.png">
            <div class="pl-cover"><img src="https://file.kxlove.top/view.php/a2d2a15fdb941e06e02634ab286fd71d.png" alt="封面"></div>
            <div class="pl-info">
                <div class="pl-title">Ocean Waves</div>
                <div class="pl-artist">Deep Blue</div>
            </div>
            <span class="pl-duration">5:08</span>
        </div>
        <div class="playlist-item" data-index="3" data-src="https://file.kxlove.top/view.php/41110a6125f7624b2376677cef0d82aa.aac" data-cover="https://file.kxlove.top/view.php/a2d2a15fdb941e06e02634ab286fd71d.png">
            <div class="pl-cover"><img src="https://file.kxlove.top/view.php/a2d2a15fdb941e06e02634ab286fd71d.png" alt="封面"></div>
            <div class="pl-info">
                <div class="pl-title">Midnight Glow</div>
                <div class="pl-artist">Neon Pulse</div>
            </div>
            <span class="pl-duration">3:28</span>
        </div>
        <div class="playlist-item" data-index="4" data-src="https://file.kxlove.top/view.php/41110a6125f7624b2376677cef0d82aa.aac" data-cover="https://file.kxlove.top/view.php/a2d2a15fdb941e06e02634ab286fd71d.png">
            <div class="pl-cover"><img src="https://file.kxlove.top/view.php/a2d2a15fdb941e06e02634ab286fd71d.png" alt="封面"></div>
            <div class="pl-info">
                <div class="pl-title">Velvet Sky</div>
                <div class="pl-artist">Dream Layer</div>
            </div>
            <span class="pl-duration">4:52</span>
        </div>
    </div>
</div>

<!-- ===== 新闻头条（播放列表下方）===== -->
<div class="news-panel" id="news-panel">
    <div class="news-header">
        <div class="news-title">
            <svg viewBox="0 0 24 24"><path d="M22 3l-1.67 1.67L18.67 3 17 4.67 15.33 3l-1.66 1.67L12 3l-1.67 1.67L8.67 3 7 4.67 5.33 3 3.67 4.67 2 3v16c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V3zm-9 12H9v2H7v-2H5v-2h2v-2h2v2h4v2zm4-2h-2v2h2v-2zm0-4h-2v2h2V9z"/></svg>
            新闻头条
        </div>
    </div>
    <div class="news-items">
        <!-- 由后台 API 数据驱动，无数据时显示占位提示 -->
        <div class="news-empty" style="padding:20px;text-align:center;color:var(--icon-color);opacity:0.5;font-size:12px">暂无新闻数据，请在后台添加</div>
    </div>
</div>

<!-- ===== 公告窗口 ===== -->
<div class="notice-overlay" id="noticeOverlay">
    <div class="notice-card">
        <div class="notice-icon">
            <svg viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/></svg>
        </div>
        <div class="notice-title">站点公告</div>
        <div class="notice-body">
            欢迎来到液态玻璃导航站！本站已全面升级为 iOS 26 液态玻璃风格，支持深浅主题切换、多搜索引擎选择、每日一言等功能。如有建议或反馈，请点击「查看」了解更多详情。
        </div>
        <div class="notice-actions">
            <button class="notice-btn notice-btn-secondary" id="noticeClose">关闭</button>
            <button class="notice-btn notice-btn-primary" id="noticeView">查看</button>
        </div>
    </div>
</div>

<script src="admin/api/settings.js"></script>
<script>
    // ===== 公告窗口逻辑 =====
    const noticeOverlay = document.getElementById('noticeOverlay');
    const noticeClose = document.getElementById('noticeClose');
    const noticeView = document.getElementById('noticeView');

    // 从后端API加载数据
    function loadPublicData() {
        return fetch('admin/api.php?action=public_data')
            .then(function(res) { return res.json(); })
            .then(function(data) {
                if (data.code === 0 && data.data) {
                    applyPublicData(data.data);
                    return data.data;
                }
                return null;
            })
            .catch(function() {
                // 降级使用 NavSettings（localStorage）
                if (window.NavSettings) {
                    const _st = NavSettings.getAll();
                    applyPublicData(_st);
                    return _st;
                }
                return null;
            });
    }

    function applyPublicData(st) {
        if (!st) return;
        window._publicData = st;
        // 最新公告内容
        const _notices = st.notices || [];
        if (_notices.length) {
            const latest = _notices[_notices.length - 1];
            const _t = noticeOverlay.querySelector('.notice-title');
            const _b = noticeOverlay.querySelector('.notice-body');
            if (_t && latest.title) _t.textContent = latest.title;
            if (_b && latest.content) _b.textContent = latest.content;
        }
        // 网站标题
        if (st.settings && st.settings.siteTitle) document.title = st.settings.siteTitle;
        else if (st.siteTitle) document.title = st.siteTitle;
        // 网站图标 favicon
        const favicon = (st.settings && st.settings.siteFavicon) || st.siteFavicon;
        if (favicon) {
            let _link = document.querySelector("link[rel*='icon']");
            if (!_link) { _link = document.createElement('link'); _link.rel = 'icon'; document.head.appendChild(_link); }
            _link.href = favicon;
        }
        // 应用显示开关
        if (st.settings) {
            const _toggle = (sel, show) => { const el = document.querySelector(sel); if (el) el.style.display = show ? '' : 'none'; };
            _toggle('#music-island', st.settings.showDynamicIsland !== false);
            _toggle('#playlist-panel', st.settings.showMusicList !== false);
            _toggle('#dev-panel', st.settings.showProgressPanel !== false);
            _toggle('#news-panel', st.settings.showNews !== false);
            _toggle('.clock-panel', st.settings.showSearchTime !== false);
            _toggle('#quote-panel', st.settings.showDailyQuote !== false);
        }
        // 后端数据返回后刷新导航卡片（首页卡片 / 推荐卡片）
        if (window.refreshSiteCards) window.refreshSiteCards();
        // 刷新进度面板 & 新闻头条（解决异步竞态：API 返回后重新渲染）
        if (window.renderProgressPanel) window.renderProgressPanel();
        if (window.renderNewsPanel) window.renderNewsPanel();
    }

    // 记录访问量
    function recordVisit() {
        fetch('admin/api.php?action=visit').catch(function() {
            // 降级
            if (window.NavSettings) NavSettings.recordVisit();
        });
    }

    // 初始化加载
    loadPublicData().finally(function() {
        // 页面加载后显示公告
        noticeOverlay.classList.add('show');
        recordVisit();
    });

    // 关闭按钮
    noticeClose.addEventListener('click', () => {
        noticeOverlay.classList.remove('show');
    });

    // 查看按钮
    noticeView.addEventListener('click', () => {
        const notices = (window._publicData && window._publicData.notices) || [];
        if (notices.length && notices[0].url) {
            window.open(notices[0].url, '_blank');
        } else {
            window.open('https://kxlove.top', '_blank');
        }
        noticeOverlay.classList.remove('show');
    });

    // 点击遮罩层关闭
    noticeOverlay.addEventListener('click', (e) => {
        if (e.target === noticeOverlay) noticeOverlay.classList.remove('show');
    });

    // ESC 关闭
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') noticeOverlay.classList.remove('show');
    });
</script>

</body>

<footer>
    <!-- ===== 液态玻璃导航栏主体 ===== -->
    <nav class="liquid-nav" id="nav">
        <div class="liquid-glare-container">
            <div class="liquid-glare" id="glare"></div>
        </div>

        <div class="nav-items">
            <div class="active-pill" id="active-pill"></div>
            <style>
                @media (max-width: 768px) {
                  .nav-btn .btn-content {
                    justify-content: center;
                  }
                  .nav-btn .btn-content #liquid-bu {
                    display: none !important;
                  }
                }
            </style>
            <button class="nav-btn active" data-view="home">
                <div class="btn-content">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                        <polyline points="9 22 9 12 15 12 15 22"></polyline>
                    </svg>
                    <span id="liquid-bu">首页</span>
                </div>
            </button>

            <button class="nav-btn" data-view="recommend">
                <div class="btn-content">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
                        <path d="M16 3.128a4 4 0 0 1 0 7.744"></path>
                        <path d="M22 21v-2a4 4 0 0 0-3-3.87"></path>
                        <circle cx="9" cy="7" r="4"></circle>
                    </svg>
                    <span id="liquid-bu">推荐</span>
                </div>
            </button>

            <button class="nav-btn" data-view="add">
                <div class="btn-content">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"/>
                        <path d="M8 12h8"/>
                        <path d="M12 8v8"/>
                    </svg>
                    <span id="liquid-bu">添加</span>
                </div>
            </button>
        </div>

        <div class="divider"></div>

        <button class="theme-btn" id="theme-btn" aria-label="Dark Mode Toggle">
            <div class="theme-icon-wrapper">
                <svg class="sun" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="5"></circle>
                    <line x1="12" y1="1" x2="12" y2="3"></line>
                    <line x1="12" y1="21" x2="12" y2="23"></line>
                    <line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line>
                    <line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line>
                    <line x1="1" y1="12" x2="3" y2="12"></line>
                    <line x1="21" y1="12" x2="23" y2="12"></line>
                    <line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line>
                    <line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line>
                </svg>
                <svg class="moon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path>
                </svg>
            </div>
        </button>
    </nav>

    <!-- ===== JavaScript 交互逻辑 ===== -->
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            // ===== 应用后台显示开关 =====
            if (window.NavSettings) {
                const _ns = NavSettings.getAll();
                const _toggle = (sel, show) => { const el = document.querySelector(sel); if (el) el.style.display = show ? '' : 'none'; };
                _toggle('#music-island', _ns.showDynamicIsland);     // 灵动岛
                _toggle('#playlist-panel', _ns.showMusicList);       // 音乐列表
                _toggle('#dev-panel', _ns.showProgressPanel);        // 进度条面板
                _toggle('#news-panel', _ns.showNews);                // 新闻头条
                _toggle('.clock-panel', _ns.showSearchTime);         // 搜索框上方时间
                _toggle('#quote-panel', _ns.showDailyQuote);         // 每日一言
            }

            // ===== 顶部灵动岛音乐播放器 =====
            const musicIsland = document.getElementById('music-island');
            const musicPanel = musicIsland ? musicIsland.querySelector('.music-panel') : null;
            const musicCompact = musicIsland ? musicIsland.querySelector('.music-compact') : null;
            const musicPlayButton = musicIsland ? musicIsland.querySelector('.music-control.play') : null;
            const musicPrevBtn = musicIsland ? musicIsland.querySelector('.music-control[aria-label="Previous"]') : null;
            const musicNextBtn = musicIsland ? musicIsland.querySelector('.music-control[aria-label="Next"]') : null;
            const musicAudio = document.getElementById('music-audio');
            const musicProgressTrack = document.getElementById('music-progress-track');
            const musicProgressBar = document.getElementById('music-progress-bar');
            const musicCurrentTime = document.getElementById('music-current-time');
            const musicDuration = document.getElementById('music-duration');
            const musicBars = musicIsland ? musicIsland.querySelectorAll('.music-equalizer span') : [];
            let musicBarsTimer = null;
            let isDraggingProgress = false;

            // ===== 播放列表 =====
            let musicPlaylist = [];
            let currentSongIndex = 0;

            function initPlaylist() {
                // 优先从 API 数据读取
                if (window._publicData && window._publicData.musicPlaylist && window._publicData.musicPlaylist.length) {
                    musicPlaylist = window._publicData.musicPlaylist.map(function(item) {
                        return {
                            title: item.title || '未知歌曲',
                            artist: item.artist || '未知艺术家',
                            src: item.src || '',
                            cover: item.cover || ''
                        };
                    });
                } else if (window.NavSettings) {
                    const saved = NavSettings.get('musicPlaylist');
                    if (saved && saved.length) {
                        musicPlaylist = saved.map(function(item) {
                            return {
                                title: item.title || '未知歌曲',
                                artist: item.artist || '未知艺术家',
                                src: item.src || '',
                                cover: item.cover || ''
                            };
                        });
                    }
                }
                // 兜底：使用页面硬编码的播放列表
                if (!musicPlaylist.length) {
                    const items = document.querySelectorAll('#playlist-items .playlist-item');
                    items.forEach(function(el) {
                        musicPlaylist.push({
                            title: el.querySelector('.pl-title').textContent,
                            artist: el.querySelector('.pl-artist').textContent,
                            src: el.dataset.src,
                            cover: el.dataset.cover
                        });
                    });
                }
                renderPlaylistPanel();
                loadSong(0);
            }

            function renderPlaylistPanel() {
                const container = document.getElementById('playlist-items');
                const countEl = document.getElementById('playlist-count');
                if (!container || !musicPlaylist.length) return;
                container.innerHTML = musicPlaylist.map(function(song, i) {
                    return '<div class="playlist-item' + (i === currentSongIndex ? ' active' : '') + '" data-index="' + i + '">' +
                        '<div class="pl-cover"><img src="' + (song.cover || '') + '" alt="封面"></div>' +
                        '<div class="pl-info">' +
                        '<div class="pl-title">' + song.title + '</div>' +
                        '<div class="pl-artist">' + song.artist + '</div>' +
                        '</div>' +
                        '<span class="pl-duration">' + (i === currentSongIndex ? '播放中' : '') + '</span>' +
                        '</div>';
                }).join('');
                if (countEl) countEl.textContent = musicPlaylist.length + ' 首';
                // 绑定点击事件
                container.querySelectorAll('.playlist-item').forEach(function(el) {
                    el.addEventListener('click', function() {
                        const idx = parseInt(el.dataset.index, 10);
                        loadSong(idx);
                        if (musicAudio) musicAudio.play().catch(function() {});
                    });
                });
            }

            function loadSong(index) {
                if (!musicPlaylist.length) return;
                currentSongIndex = (index + musicPlaylist.length) % musicPlaylist.length;
                const song = musicPlaylist[currentSongIndex];

                // 更新 audio
                if (musicAudio && song.src) {
                    musicAudio.src = song.src;
                    musicAudio.load();
                }

                // 更新紧凑视图
                if (musicCompact) {
                    const compactCover = musicCompact.querySelector('.music-cover img');
                    const compactTitle = musicCompact.querySelector('.music-title');
                    const compactArtist = musicCompact.querySelector('.music-artist');
                    if (compactCover) compactCover.src = song.cover || '';
                    if (compactTitle) compactTitle.textContent = song.title;
                    if (compactArtist) compactArtist.textContent = song.artist;
                }

                // 更新展开面板
                if (musicPanel) {
                    const panelCover = musicPanel.querySelector('.music-panel-cover img');
                    const panelTitle = musicPanel.querySelector('.music-panel-title');
                    const panelArtist = musicPanel.querySelector('.music-panel-artist');
                    if (panelCover) panelCover.src = song.cover || '';
                    if (panelTitle) panelTitle.textContent = song.title;
                    if (panelArtist) panelArtist.textContent = song.artist;
                }

                // 更新播放列表高亮
                const items = document.querySelectorAll('#playlist-items .playlist-item');
                items.forEach(function(el, i) {
                    el.classList.toggle('active', i === currentSongIndex);
                });

                // 更新音频事件绑定
                if (musicAudio) updateMusicProgress();
            }

            function nextSong() { loadSong(currentSongIndex + 1); }
            function prevSong() { loadSong(currentSongIndex - 1); }

            function setMusicIslandExpanded(expanded) {
                if (!musicIsland) return;
                musicIsland.classList.toggle('expanded', expanded);
                musicIsland.setAttribute('aria-expanded', String(expanded));
                if (musicPanel) musicPanel.setAttribute('aria-hidden', String(!expanded));
                if (musicCompact) musicCompact.setAttribute('aria-hidden', String(expanded));
            }

            function randomizeMusicBars() {
                musicBars.forEach((bar, index) => {
                    const floor = index % 2 === 0 ? 5 : 7;
                    const height = floor + Math.round(Math.random() * 16);
                    bar.style.height = `${height}px`;
                    bar.style.opacity = String(0.5 + Math.random() * 0.42);
                });
            }

            function setMusicPlaying(isPlaying) {
                if (!musicIsland || !musicPlayButton) return;
                musicIsland.classList.toggle('is-playing', isPlaying);
                musicPlayButton.setAttribute('aria-pressed', String(isPlaying));
                musicPlayButton.innerHTML = isPlaying
                    ? '<svg width="22" height="22" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M7 5h4v14H7zM13 5h4v14h-4z"/></svg>'
                    : '<svg width="22" height="22" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M8 5v14l11-7z"/></svg>';

                if (musicBarsTimer) {
                    clearInterval(musicBarsTimer);
                    musicBarsTimer = null;
                }

                if (isPlaying) {
                    randomizeMusicBars();
                    musicBarsTimer = setInterval(randomizeMusicBars, 170);
                } else {
                    musicBars.forEach((bar, index) => {
                        bar.style.height = `${index % 2 === 0 ? 8 : 11}px`;
                        bar.style.opacity = '0.72';
                    });
                }
            }

            function formatMusicTime(seconds) {
                if (!Number.isFinite(seconds) || seconds < 0) return '0:00';
                const minutes = Math.floor(seconds / 60);
                const rest = Math.floor(seconds % 60).toString().padStart(2, '0');
                return `${minutes}:${rest}`;
            }

            function updateMusicProgress() {
                if (!musicAudio || isDraggingProgress) return;
                const duration = musicAudio.duration || 0;
                const current = musicAudio.currentTime || 0;
                if (musicProgressBar) {
                    const percent = duration ? Math.min((current / duration) * 100, 100) : 0;
                    musicProgressBar.style.width = `${percent}%`;
                }
                if (musicCurrentTime) musicCurrentTime.textContent = formatMusicTime(current);
                if (musicDuration) musicDuration.textContent = formatMusicTime(duration);
            }

            function seekByPointerEvent(e) {
                if (!musicAudio || !musicProgressTrack || !musicAudio.duration) return;
                const rect = musicProgressTrack.getBoundingClientRect();
                const offsetX = e.clientX - rect.left;
                const percent = Math.max(0, Math.min(1, offsetX / rect.width));
                
                if (musicProgressBar) musicProgressBar.style.width = `${percent * 100}%`;
                if (musicCurrentTime) musicCurrentTime.textContent = formatMusicTime(percent * musicAudio.duration);
                
                return percent * musicAudio.duration;
            }

            if (musicProgressTrack) {
                musicProgressTrack.addEventListener('pointerdown', (e) => {
                    isDraggingProgress = true;
                    musicProgressTrack.setPointerCapture(e.pointerId);
                    const seekTime = seekByPointerEvent(e);
                    if (seekTime !== undefined) musicAudio.currentTime = seekTime;
                });

                musicProgressTrack.addEventListener('pointermove', (e) => {
                    if (!isDraggingProgress) return;
                    const seekTime = seekByPointerEvent(e);
                    if (seekTime !== undefined) musicAudio.currentTime = seekTime;
                });

                const stopDrag = (e) => {
                    if (isDraggingProgress) {
                        isDraggingProgress = false;
                        try {
                            musicProgressTrack.releasePointerCapture(e.pointerId);
                        } catch (err) {}
                    }
                };

                musicProgressTrack.addEventListener('pointerup', stopDrag);
                musicProgressTrack.addEventListener('pointercancel', stopDrag);
            }

            if (musicIsland) {
                musicIsland.addEventListener('click', (e) => {
                    if (e.target.closest('.music-control') || e.target.closest('.music-progress')) return;
                    setMusicIslandExpanded(!musicIsland.classList.contains('expanded'));
                });

                if (musicPlayButton) {
                    musicPlayButton.addEventListener('click', async () => {
                        if (!musicAudio) {
                            setMusicPlaying(!musicIsland.classList.contains('is-playing'));
                            return;
                        }

                        if (musicAudio.paused) {
                            try {
                                await musicAudio.play();
                            } catch (error) {
                                console.warn('Music playback failed:', error);
                                setMusicPlaying(false);
                            }
                        } else {
                            musicAudio.pause();
                        }
                    });
                }

                // 上一首 / 下一首
                if (musicPrevBtn) {
                    musicPrevBtn.addEventListener('click', () => {
                        prevSong();
                        if (musicAudio) musicAudio.play().catch(function() {});
                    });
                }
                if (musicNextBtn) {
                    musicNextBtn.addEventListener('click', () => {
                        nextSong();
                        if (musicAudio) musicAudio.play().catch(function() {});
                    });
                }

                if (musicAudio) {
                    musicAudio.addEventListener('play', () => setMusicPlaying(true));
                    musicAudio.addEventListener('pause', () => setMusicPlaying(false));
                    musicAudio.addEventListener('ended', () => {
                        setMusicPlaying(false);
                        // 自动播放下一首
                        if (musicPlaylist.length > 1) {
                            nextSong();
                            musicAudio.play().catch(function() {});
                        }
                    });
                    musicAudio.addEventListener('loadedmetadata', updateMusicProgress);
                    musicAudio.addEventListener('timeupdate', updateMusicProgress);
                    updateMusicProgress();
                }

                musicIsland.addEventListener('keydown', (e) => {
                    if (e.key === 'Enter' || e.key === ' ') {
                        e.preventDefault();
                        setMusicIslandExpanded(!musicIsland.classList.contains('expanded'));
                    }
                    if (e.key === 'Escape') {
                        setMusicIslandExpanded(false);
                    }
                });

                document.addEventListener('click', (e) => {
                    if (!musicIsland.contains(e.target)) {
                        setMusicIslandExpanded(false);
                    }
                });
            }

            // ===== 初始化播放列表（必须在所有变量声明后调用）=====
            initPlaylist();

            // ===== 导航栏功能 =====
            const navButtons = document.querySelectorAll(".nav-btn");
            const activePill = document.getElementById("active-pill");
            const themeBtn = document.getElementById("theme-btn");
            const nav = document.getElementById("nav");
            const glare = document.getElementById("glare");

            function updatePill(btn, smooth = true) {
                if (!btn) return;
                if (!smooth) {
                    activePill.style.transition = 'none';
                } else {
                    activePill.style.transition = 'transform 0.5s cubic-bezier(0.34, 1.2, 0.64, 1), width 0.5s cubic-bezier(0.34, 1.2, 0.64, 1), background 0.5s ease, box-shadow 0.5s ease';
                }
                activePill.style.width = `${btn.offsetWidth}px`;
                activePill.style.transform = `translateX(${btn.offsetLeft}px)`;
            }

            // ===== 每日一言 =====
            const quotePanel = document.getElementById('quote-panel');
            const quoteDate = document.getElementById('quote-date');
            const quoteContent = document.getElementById('quote-content');
            const quoteAuthor = document.getElementById('quote-author');

            function fetchQuote() {
                fetch('https://v1.hitokoto.cn')
                    .then(res => res.json())
                    .then(data => {
                        if (quoteContent) quoteContent.textContent = data.hitokoto || '生活就像海洋，只有意志坚强的人才能到达彼岸。';
                        if (quoteAuthor) quoteAuthor.textContent = `— ${data.from || '马克思'}`;
                    })
                    .catch(() => {
                        if (quoteContent) quoteContent.textContent = '生活就像海洋，只有意志坚强的人才能到达彼岸。';
                        if (quoteAuthor) quoteAuthor.textContent = '— 马克思';
                    });

                if (quoteDate) {
                    const now = new Date();
                    quoteDate.textContent = `${now.getFullYear()}.${(now.getMonth()+1).toString().padStart(2,'0')}.${now.getDate().toString().padStart(2,'0')}`;
                }
            }
            fetchQuote();

            // 每日一言面板拖拽
            const quoteHeader = quotePanel ? quotePanel.querySelector('.quote-header') : null;
            if (quotePanel && quoteHeader) {
                let isDraggingQuote = false;
                let dragOffsetXQuote = 0, dragOffsetYQuote = 0;

                quoteHeader.addEventListener('mousedown', (e) => {
                    if (e.target.closest('.quote-date')) return;
                    isDraggingQuote = true;
                    quotePanel.classList.add('dragging');
                    const rect = quotePanel.getBoundingClientRect();
                    dragOffsetXQuote = e.clientX - rect.left;
                    dragOffsetYQuote = e.clientY - rect.top;
                    quotePanel.style.left = rect.left + 'px';
                    quotePanel.style.top = rect.top + 'px';
                    quotePanel.style.transform = 'none';
                    e.preventDefault();
                });

                document.addEventListener('mousemove', (e) => {
                    if (!isDraggingQuote) return;
                    let newX = e.clientX - dragOffsetXQuote;
                    let newY = e.clientY - dragOffsetYQuote;
                    newX = Math.max(8, Math.min(newX, window.innerWidth - quotePanel.offsetWidth - 8));
                    newY = Math.max(8, Math.min(newY, window.innerHeight - quotePanel.offsetHeight - 8));
                    quotePanel.style.left = newX + 'px';
                    quotePanel.style.top = newY + 'px';
                });

                document.addEventListener('mouseup', () => {
                    if (!isDraggingQuote) return;
                    isDraggingQuote = false;
                    quotePanel.classList.remove('dragging');
                });
            }

            // ===== 开发进度表拖拽 =====
            const devPanel = document.getElementById('dev-panel');
            const devHeader = devPanel ? devPanel.querySelector('.dev-header') : null;
            if (devPanel && devHeader) {
                let isDraggingDev = false;
                let dragOffsetXDev = 0, dragOffsetYDev = 0;

                devHeader.addEventListener('mousedown', (e) => {
                    if (e.target.closest('.dev-date')) return;
                    isDraggingDev = true;
                    devPanel.classList.add('dragging');
                    const rect = devPanel.getBoundingClientRect();
                    dragOffsetXDev = e.clientX - rect.left;
                    dragOffsetYDev = e.clientY - rect.top;
                    devPanel.style.left = rect.left + 'px';
                    devPanel.style.top = rect.top + 'px';
                    devPanel.style.transform = 'none';
                    e.preventDefault();
                });

                document.addEventListener('mousemove', (e) => {
                    if (!isDraggingDev) return;
                    let newX = e.clientX - dragOffsetXDev;
                    let newY = e.clientY - dragOffsetYDev;
                    newX = Math.max(8, Math.min(newX, window.innerWidth - devPanel.offsetWidth - 8));
                    newY = Math.max(8, Math.min(newY, window.innerHeight - devPanel.offsetHeight - 8));
                    devPanel.style.left = newX + 'px';
                    devPanel.style.top = newY + 'px';
                });

                document.addEventListener('mouseup', () => {
                    if (!isDraggingDev) return;
                    isDraggingDev = false;
                    devPanel.classList.remove('dragging');
                });
            }

            // ===== 播放列表拖拽 =====
            const playlistPanel = document.getElementById('playlist-panel');
            const playlistHeader = playlistPanel ? playlistPanel.querySelector('.playlist-header') : null;
            if (playlistPanel && playlistHeader) {
                let isDragging = false;
                let dragOffsetX = 0, dragOffsetY = 0;

                playlistHeader.addEventListener('mousedown', (e) => {
                    // 不拦截按钮点击
                    if (e.target.closest('.playlist-count')) return;
                    isDragging = true;
                    playlistPanel.classList.add('dragging');
                    const rect = playlistPanel.getBoundingClientRect();
                    dragOffsetX = e.clientX - rect.left;
                    dragOffsetY = e.clientY - rect.top;
                    // 切换为 left/top 定位，移除 right/transform
                    playlistPanel.style.left = rect.left + 'px';
                    playlistPanel.style.top = rect.top + 'px';
                    playlistPanel.style.right = 'auto';
                    playlistPanel.style.transform = 'none';
                    e.preventDefault();
                });

                document.addEventListener('mousemove', (e) => {
                    if (!isDragging) return;
                    let newX = e.clientX - dragOffsetX;
                    let newY = e.clientY - dragOffsetY;
                    // 边界限制
                    newX = Math.max(8, Math.min(newX, window.innerWidth - playlistPanel.offsetWidth - 8));
                    newY = Math.max(8, Math.min(newY, window.innerHeight - playlistPanel.offsetHeight - 8));
                    playlistPanel.style.left = newX + 'px';
                    playlistPanel.style.top = newY + 'px';
                });

                document.addEventListener('mouseup', () => {
                    if (!isDragging) return;
                    isDragging = false;
                    playlistPanel.classList.remove('dragging');
                });
            }

            // ===== 新闻头条拖拽 =====
            const newsPanel = document.getElementById('news-panel');
            const newsHeader = newsPanel ? newsPanel.querySelector('.news-header') : null;
            if (newsPanel && newsHeader) {
                let isDraggingNews = false;
                let dragOffsetXNews = 0, dragOffsetYNews = 0;

                newsHeader.addEventListener('mousedown', (e) => {
                    isDraggingNews = true;
                    newsPanel.classList.add('dragging');
                    const rect = newsPanel.getBoundingClientRect();
                    dragOffsetXNews = e.clientX - rect.left;
                    dragOffsetYNews = e.clientY - rect.top;
                    newsPanel.style.left = rect.left + 'px';
                    newsPanel.style.top = rect.top + 'px';
                    newsPanel.style.right = 'auto';
                    newsPanel.style.transform = 'none';
                    e.preventDefault();
                });

                document.addEventListener('mousemove', (e) => {
                    if (!isDraggingNews) return;
                    let newX = e.clientX - dragOffsetXNews;
                    let newY = e.clientY - dragOffsetYNews;
                    newX = Math.max(8, Math.min(newX, window.innerWidth - newsPanel.offsetWidth - 8));
                    newY = Math.max(8, Math.min(newY, window.innerHeight - newsPanel.offsetHeight - 8));
                    newsPanel.style.left = newX + 'px';
                    newsPanel.style.top = newY + 'px';
                });

                document.addEventListener('mouseup', () => {
                    if (!isDraggingNews) return;
                    isDraggingNews = false;
                    newsPanel.classList.remove('dragging');
                });
            }

            // ===== 播放列表点击切换 =====
            const playlistItems = document.querySelectorAll('.playlist-item');
            const plAudio = document.getElementById('music-audio');
            const plTitle = musicIsland ? musicIsland.querySelector('.music-panel-title') : null;
            const plArtist = musicIsland ? musicIsland.querySelector('.music-panel-artist') : null;
            const plCompactTitle = musicIsland ? musicIsland.querySelector('.music-title') : null;
            const plCompactArtist = musicIsland ? musicIsland.querySelector('.music-artist') : null;
            const plCoverImgs = musicIsland ? musicIsland.querySelectorAll('.music-cover img') : [];

            playlistItems.forEach(item => {
                item.addEventListener('click', () => {
                    playlistItems.forEach(i => i.classList.remove('active'));
                    item.classList.add('active');

                    const src = item.getAttribute('data-src');
                    const cover = item.getAttribute('data-cover');
                    const title = item.querySelector('.pl-title').textContent;
                    const artist = item.querySelector('.pl-artist').textContent;

                    if (plAudio && src) {
                        plAudio.src = src;
                        plAudio.play().catch(() => {});
                    }
                    if (plTitle) plTitle.textContent = title;
                    if (plArtist) plArtist.textContent = artist;
                    if (plCompactTitle) plCompactTitle.textContent = title;
                    if (plCompactArtist) plCompactArtist.textContent = artist;
                    plCoverImgs.forEach(img => { if (cover) img.src = cover; });

                    if (musicPlayButton) {
                        musicPlayButton.setAttribute('aria-pressed', 'true');
                        musicPlayButton.innerHTML = '<svg width="22" height="22" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M6 19h4V5H6v14zm8-14v14h4V5h-4z"/></svg>';
                    }
                });
            });

            // ===== 首页 / 推荐导航卡片（扁平数组，支持下滑加载更多）=====
            // 默认各 6 张；后台添加更多后，用户下滑即可加载下一批
            const siteBatches = {
                home: [
                    { title:'百度', url:'https://www.baidu.com', icon:'https://www.baidu.com/favicon.ico', tags:[{t:'热门',c:'hot'},{t:'搜索'}], desc:'全球最大的中文搜索引擎，提供网页、图片、新闻、地图等全方位搜索服务。' },
                    { title:'必应', url:'https://www.bing.com', icon:'https://www.bing.com/favicon.ico', tags:[{t:'热门',c:'hot'},{t:'搜索'}], desc:'微软推出的搜索引擎，提供精准的搜索结果和每日精美壁纸。' },
                    { title:'谷歌', url:'https://www.google.com', icon:'https://www.google.com/favicon.ico', tags:[{t:'热门',c:'hot'},{t:'搜索'}], desc:'全球最大的搜索引擎，提供快速、精准的搜索体验和丰富的在线服务。' },
                    { title:'CSDN', url:'https://www.csdn.net', icon:'https://www.csdn.net/favicon.ico', tags:[{t:'工具',c:'tool'},{t:'开发'}], desc:'中文开发者技术社区，提供编程教程、技术博客和开源项目资源。' },
                    { title:'GitHub', url:'https://github.com', icon:'https://github.githubassets.com/favicons/favicon.svg', tags:[{t:'工具',c:'tool'},{t:'开源'}], desc:'全球最大的代码托管平台，数百万开发者的协作与开源项目聚集地。' },
                    { title:'Dribbble', url:'https://dribbble.com', icon:'https://cdn.dribbble.com/assets/favicon-b38525134603b9513174ec887944bde1a869eb6cd414f4d640ee48eb2a29a0f9.png', tags:[{t:'美化',c:'beauty'},{t:'设计'}], desc:'设计师作品展示社区，发现全球顶尖的 UI/UX 设计灵感和创意作品。' }
                ],
                recommend: [
                    { title:'微博', url:'https://weibo.com', icon:'https://weibo.com/favicon.ico', tags:[{t:'热门',c:'hot'},{t:'社交'}], desc:'中文社交媒体平台，实时获取热点资讯与明星动态。' },
                    { title:'抖音', url:'https://www.douyin.com', icon:'https://www.douyin.com/favicon.ico', tags:[{t:'热门',c:'hot'},{t:'视频'}], desc:'短视频社交平台，记录美好生活，发现有趣内容。' },
                    { title:'npm', url:'https://www.npmjs.com', icon:'https://www.npmjs.com/favicon.ico', tags:[{t:'工具',c:'tool'},{t:'开发'}], desc:'Node 包管理仓库，海量开源 JavaScript 包供开发者使用。' },
                    { title:'CodePen', url:'https://codepen.io', icon:'https://cpwebassets.codepen.io/assets/favicon/favicon-aec34940fbc1a6e787974dcd360f2c6b63348d4b1f4e06977192aa5171f7d8ef.png', tags:[{t:'工具',c:'tool'},{t:'代码'}], desc:'在线前端代码编辑与作品分享社区，快速构建与展示创意原型。' },
                    { title:'Unsplash', url:'https://unsplash.com', icon:'https://unsplash.com/favicon.ico', tags:[{t:'美化',c:'beauty'},{t:'图片'}], desc:'免费高清图片素材网站，提供高质量摄影作品供免费下载商用。' },
                    { title:'Figma', url:'https://www.figma.com', icon:'https://static.figma.com/app/icon/1/favicon.svg', tags:[{t:'美化',c:'beauty'},{t:'设计'}], desc:'在线协作设计工具，支持团队实时协作的界面设计与原型制作。' }
                ]
            };

            // 标签归一化：后端存储为字符串数组（如 ['hot','tool']），前端需统一为 {t,c} 对象格式
            const TAG_LABELS = { hot: '热门', tool: '工具', beauty: '美化' };
            function normalizeCard(c) {
                if (!c) return null;
                let tags = Array.isArray(c.tags) ? c.tags : [];
                // 字符串标签 → 对象
                if (tags.length && typeof tags[0] === 'string') {
                    tags = tags.map(tg => TAG_LABELS[tg] ? { t: TAG_LABELS[tg], c: tg } : { t: tg });
                }
                return {
                    id: c.id || '',
                    title: c.title || '未命名',
                    url: c.url || '#',
                    icon: c.icon || '',
                    desc: c.desc || '',
                    tags: tags
                };
            }
            function normalizeList(list) {
                return Array.isArray(list) ? list.map(normalizeCard).filter(Boolean) : [];
            }

            // 从后端API或本地缓存覆盖卡片数据
            function applyCardsFromData() {
                const src = window._publicData || (window.NavSettings ? NavSettings.getAll() : {});
                if (src.homeCards && src.homeCards.length) siteBatches.home = normalizeList(src.homeCards);
                if (src.recommendCards && src.recommendCards.length) {
                    siteBatches.recommend = normalizeList(src.recommendCards);
                } else if (src.recommendBatches && src.recommendBatches.length) {
                    // 兼容旧批次结构（数组嵌套数组）→ 拍平
                    const flat = [];
                    src.recommendBatches.forEach(b => { if (Array.isArray(b)) flat.push(...b); else flat.push(b); });
                    siteBatches.recommend = normalizeList(flat);
                }
            }
            applyCardsFromData();

            const siteCardsEl = document.getElementById('site-cards');
            const addFormEl = document.getElementById('add-form');
            let currentView = 'home';
            // 渐进式加载：每次展示 PAGE_SIZE 张，下滑到底自动加载下一批
            const CARD_PAGE_SIZE = 6;
            let currentCards = [];
            let displayCount = 0;
            let loadMoreObserver = null;
            let isLoadingMore = false;

            // 单张卡片 HTML
            function cardHtml(c) {
                const tagsHtml = (c.tags || []).map(tg => `<span class="card-tag${tg.c ? ' ' + tg.c : ''}">${tg.t}</span>`).join('');
                return `<a class="site-card" href="${c.url}" target="_blank" rel="noopener">
                    <div class="card-head">
                        <div class="card-icon"><img src="${c.icon}" alt="${c.title}" onerror="this.style.visibility='hidden'"></div>
                        <div class="card-info">
                            <div class="card-title">${c.title}</div>
                            <div class="card-tags">${tagsHtml}</div>
                        </div>
                    </div>
                    <div class="card-desc">${c.desc}</div>
                    <span class="card-visit">访问 →</span>
                </a>`;
            }

            // 加载更多按钮 / 到底提示
            function footerHtml() {
                const remaining = currentCards.length - displayCount;
                if (remaining > 0) {
                    return `<div class="load-more-wrap" id="loadMoreWrap">
                        <button class="load-more-btn" id="loadMoreBtn" type="button">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
                            加载更多（剩余 ${remaining} 个）
                        </button>
                    </div>`;
                }
                if (currentCards.length > CARD_PAGE_SIZE) {
                    return `<div class="load-more-wrap"><span class="no-more">— 已经到底了 —</span></div>`;
                }
                return '';
            }

            // 渲染卡片（切换视图时带过渡动画）
            function renderSiteCards(cards, isViewSwitch) {
                if (!siteCardsEl) return;
                currentCards = cards || [];
                displayCount = Math.min(CARD_PAGE_SIZE, currentCards.length);
                const doRender = () => {
                    siteCardsEl.innerHTML = currentCards.slice(0, displayCount).map(cardHtml).join('') + footerHtml();
                    siteCardsEl.classList.remove('swapping');
                    bindLoadMore();
                };
                if (isViewSwitch) {
                    siteCardsEl.classList.add('swapping');
                    setTimeout(doRender, 220);
                } else {
                    doRender();
                }
            }

            // 追加渲染下一批（下滑/点击加载更多）
            function loadMoreCards() {
                if (isLoadingMore || displayCount >= currentCards.length) return;
                isLoadingMore = true;
                const btn = document.getElementById('loadMoreBtn');
                if (btn) { btn.classList.add('loading'); }
                // 短暂延时，体现加载反馈
                setTimeout(() => {
                    displayCount = Math.min(displayCount + CARD_PAGE_SIZE, currentCards.length);
                    siteCardsEl.innerHTML = currentCards.slice(0, displayCount).map(cardHtml).join('') + footerHtml();
                    bindLoadMore();
                    isLoadingMore = false;
                }, 180);
            }

            // 绑定“加载更多”按钮 + IntersectionObserver 下滑自动加载
            function bindLoadMore() {
                const btn = document.getElementById('loadMoreBtn');
                if (!btn) return;
                btn.addEventListener('click', loadMoreCards);
                // 下滑到容器底部自动加载（root 为卡片容器自身）
                if (window.IntersectionObserver && loadMoreObserver) {
                    loadMoreObserver.disconnect();
                }
                if (window.IntersectionObserver) {
                    loadMoreObserver = new IntersectionObserver((entries) => {
                        entries.forEach(entry => {
                            if (entry.isIntersecting) loadMoreCards();
                        });
                    }, { root: siteCardsEl, rootMargin: '80px' });
                    loadMoreObserver.observe(btn);
                }
            }

            function setAddFormVisible(visible) {
                if (!addFormEl) return;
                if (visible) {
                    addFormEl.style.display = 'block';
                    requestAnimationFrame(() => requestAnimationFrame(() => addFormEl.classList.add('show')));
                } else {
                    addFormEl.classList.remove('show');
                    setTimeout(() => {
                        if (!addFormEl.classList.contains('show')) addFormEl.style.display = 'none';
                    }, 320);
                }
            }

            function showView(view) {
                if (view === 'add') {
                    if (siteCardsEl) siteCardsEl.style.display = 'none';
                    setAddFormVisible(true);
                    currentView = 'add';
                    return;
                }
                setAddFormVisible(false);
                if (siteCardsEl) siteCardsEl.style.display = '';
                if (view === 'home') {
                    renderSiteCards(siteBatches.home, true);
                    currentView = 'home';
                } else if (view === 'recommend') {
                    renderSiteCards(siteBatches.recommend, true);
                    currentView = 'recommend';
                }
            }

            // 暴露给 applyPublicData 调用：后端数据返回后刷新卡片（解决异步竞态）
            window.refreshSiteCards = function() {
                applyCardsFromData();
                // 当前正在展示的视图重新渲染（尽量保持已加载的数量）
                const list = currentView === 'recommend' ? siteBatches.recommend : siteBatches.home;
                if (!list || !list.length || !siteCardsEl) return;
                const keepCount = Math.min(displayCount || CARD_PAGE_SIZE, list.length);
                currentCards = list;
                displayCount = Math.max(CARD_PAGE_SIZE, keepCount);
                displayCount = Math.min(displayCount, list.length);
                siteCardsEl.innerHTML = list.slice(0, displayCount).map(cardHtml).join('') + footerHtml();
                bindLoadMore();
            };

            // 卡片点击涟漪（事件委托，适配动态渲染）
            if (siteCardsEl) {
                siteCardsEl.addEventListener('click', (e) => {
                    const card = e.target.closest('.site-card');
                    if (!card) return;
                    const rect = card.getBoundingClientRect();
                    const size = Math.max(rect.width, rect.height);
                    const ripple = document.createElement('span');
                    ripple.className = 'ripple';
                    ripple.style.width = ripple.style.height = `${size}px`;
                    ripple.style.left = `${(e.clientX || rect.left + rect.width/2) - rect.left - size/2}px`;
                    ripple.style.top = `${(e.clientY || rect.top + rect.height/2) - rect.top - size/2}px`;
                    card.appendChild(ripple);
                    setTimeout(() => ripple.remove(), 600);
                });
            }

            // 添加表单逻辑
            const addFormTextarea = document.getElementById('add-form-textarea');
            const addFormSubmit = document.getElementById('add-form-submit');
            const addFormCancel = document.getElementById('add-form-cancel');

            function showAddToast(msg) {
                let toast = document.getElementById('add-toast');
                if (!toast) {
                    toast = document.createElement('div');
                    toast.id = 'add-toast';
                    toast.className = 'add-toast';
                    document.body.appendChild(toast);
                }
                toast.textContent = msg;
                toast.classList.add('show');
                clearTimeout(toast._timer);
                toast._timer = setTimeout(() => toast.classList.remove('show'), 2200);
            }

            if (addFormSubmit) {
                addFormSubmit.addEventListener('click', () => {
                    const val = addFormTextarea ? addFormTextarea.value.trim() : '';
                    if (!val) { showAddToast('请填写站点信息'); return; }
                    // 提交到后端API
                    fetch('admin/api.php?action=feedback', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ content: val })
                    }).then(function(res) { return res.json(); })
                    .then(function(data) {
                        if (data.code === 0) {
                            showAddToast('提交成功，感谢推荐！');
                            addFormTextarea.value = '';
                        } else {
                            // 显示后端返回的提示（包括 429 限流提示）
                            showAddToast(data.msg || '提交失败');
                        }
                    }).catch(function() {
                        // 降级到本地存储
                        if (window.NavSettings) {
                            const list = NavSettings.get('feedback') || [];
                            list.push({ id: NavSettings.uid(), content: val, time: new Date().toLocaleString('zh-CN') });
                            NavSettings.set('feedback', list);
                        }
                        showAddToast('提交成功（离线）');
                        addFormTextarea.value = '';
                    });
                });
            }
            if (addFormCancel) {
                addFormCancel.addEventListener('click', () => {
                    if (addFormTextarea) addFormTextarea.value = '';
                    const homeBtn = document.querySelector('.nav-btn[data-view="home"]');
                    if (homeBtn) homeBtn.click();
                });
            }

            // 初始渲染首页卡片
            renderSiteCards(siteBatches.home);

            const initialActive = document.querySelector(".nav-btn.active");
            if (initialActive) {
                setTimeout(() => {
                    updatePill(initialActive, false);
                    void activePill.offsetWidth;
                }, 50);
            }

            navButtons.forEach(btn => {
                btn.addEventListener("click", () => {
                    navButtons.forEach(b => b.classList.remove("active"));
                    btn.classList.add("active");
                    updatePill(btn);
                    const view = btn.dataset.view;
                    if (view) showView(view);
                });
            });

            themeBtn.addEventListener("click", () => {
                const root = document.documentElement;
                const isDark = root.getAttribute("data-theme") === "dark";
                root.setAttribute("data-theme", isDark ? "light" : "dark");
                
                setTimeout(() => {
                    const active = document.querySelector(".nav-btn.active");
                    if (active) updatePill(active);
                    
                    const activeEngine = document.querySelector(".engine-btn.active");
                    if (activeEngine) updateEnginePill(activeEngine);
                }, 100);
            });

            window.addEventListener("resize", () => {
                const active = document.querySelector(".nav-btn.active");
                if (active) updatePill(active, false);
                
                const activeEngine = document.querySelector(".engine-btn.active");
                if (activeEngine) updateEnginePill(activeEngine, false);
            });

            nav.addEventListener("mousemove", (e) => {
                const rect = nav.getBoundingClientRect();
                const x = e.clientX - rect.left;
                const y = e.clientY - rect.top;
                glare.style.setProperty("--x", `${x}px`);
                glare.style.setProperty("--y", `${y}px`);
            });

            // ===== 搜索引擎切换逻辑 =====
            const engineButtons = document.querySelectorAll(".engine-btn");
            const enginePill = document.getElementById("engine-pill");
            let currentEngineUrl = "";

            function updateEnginePill(btn, smooth = true) {
                if (!btn || !enginePill) return;
                if (!smooth) {
                    enginePill.style.transition = 'none';
                } else {
                    enginePill.style.transition = 'transform 0.5s cubic-bezier(0.34, 1.2, 0.64, 1), width 0.5s cubic-bezier(0.34, 1.2, 0.64, 1), background 0.5s ease, box-shadow 0.5s ease';
                }
                enginePill.style.width = `${btn.offsetWidth}px`;
                enginePill.style.transform = `translateX(${btn.offsetLeft}px)`;
            }

            const initialEngine = document.querySelector(".engine-btn.active");
            if (initialEngine) {
                setTimeout(() => {
                    updateEnginePill(initialEngine, false);
                    void enginePill.offsetWidth;
                    currentEngineUrl = initialEngine.dataset.url;
                }, 50);
            }

            engineButtons.forEach(btn => {
                btn.addEventListener("click", () => {
                    engineButtons.forEach(b => b.classList.remove("active"));
                    btn.classList.add("active");
                    updateEnginePill(btn);
                    currentEngineUrl = btn.dataset.url;
                });
            });

            // ===== 搜索框高光与提交 =====
            const searchContainer = document.getElementById("search-container");
            const searchGlare = document.getElementById("search-glare");
            
            if (searchContainer && searchGlare) {
                searchContainer.addEventListener("mousemove", (e) => {
                    const rect = searchContainer.getBoundingClientRect();
                    const x = e.clientX - rect.left;
                    const y = e.clientY - rect.top;
                    searchGlare.style.setProperty("--sx", `${x}px`);
                    searchGlare.style.setProperty("--sy", `${y}px`);
                });
            }
            
            const searchSubmit = document.getElementById("search-submit");
            const searchInput = document.getElementById("search-input");
            
            function doSearch() {
                const value = searchInput.value.trim();
                if (value && currentEngineUrl) {
                    window.open(currentEngineUrl + encodeURIComponent(value), '_blank');
                }
            }
            
            if (searchSubmit && searchInput) {
                searchSubmit.addEventListener("click", doSearch);
                searchInput.addEventListener("keydown", (e) => {
                    if (e.key === "Enter" && !e.shiftKey) {
                        e.preventDefault();
                        doSearch();
                    }
                });
            }

            // ===== 时钟 =====
            const clockHm = document.getElementById('clock-hm');
            const clockS = document.getElementById('clock-s');
            const clockDate = document.getElementById('clock-date');
            const clockWeek = document.getElementById('clock-week');
            const weekNames = ['周日', '周一', '周二', '周三', '周四', '周五', '周六'];

            function updateClock() {
                const now = new Date();
                const h = now.getHours().toString().padStart(2, '0');
                const m = now.getMinutes().toString().padStart(2, '0');
                const s = now.getSeconds().toString().padStart(2, '0');
                if (clockHm) clockHm.textContent = `${h}:${m}`;
                if (clockS) clockS.textContent = s;
                if (clockDate) clockDate.textContent = `${now.getFullYear()}.${(now.getMonth()+1).toString().padStart(2,'0')}.${now.getDate().toString().padStart(2,'0')}`;
                if (clockWeek) clockWeek.textContent = weekNames[now.getDay()];
            }
            updateClock();
            setInterval(updateClock, 1000);

            // ===== 从API数据渲染进度面板（提取为全局函数，支持异步刷新）=====
            window.renderProgressPanel = function() {
                var data = window._publicData;
                var devItems = document.querySelector('.dev-items');
                if (!devItems || !data || !data.progressItems || !data.progressItems.length) return;
                var statusMap = {
                    done: { cls: 'done', label: '已完成' },
                    progress: { cls: 'progress', label: '开发中' },
                    warning: { cls: 'warning', label: '调试中' },
                    pending: { cls: 'pending', label: '待开发' }
                };
                devItems.innerHTML = data.progressItems.map(function(p) {
                    var s = statusMap[p.status] || statusMap.pending;
                    var percent = p.percent || 0;
                    if (p.status === 'done') percent = 100;
                    return '<div class="dev-item">' +
                        '<div class="dev-item-head">' +
                        '<span class="dev-item-name">' + (p.name || '') + '</span>' +
                        '<span class="dev-item-status ' + s.cls + '">' + s.label + '</span>' +
                        '</div>' +
                        '<div class="dev-item-bar"><div class="dev-item-fill ' + s.cls + '" style="width:' + percent + '%"></div></div>' +
                        '<div class="dev-item-desc">' + (p.desc || '') + '</div>' +
                        '</div>';
                }).join('');
            };

            // ===== 从API数据渲染新闻面板（提取为全局函数，支持异步刷新）=====
            window.renderNewsPanel = function() {
                var data = window._publicData;
                var newsItems = document.querySelector('.news-items');
                if (!newsItems || !data || !data.newsItems || !data.newsItems.length) return;
                newsItems.innerHTML = data.newsItems.map(function(n) {
                    var img = n.image ? '<div class="news-img"><img src="' + n.image + '" alt="新闻图片" onerror="this.style.display=\'none\'"></div>' : '';
                    var url = n.url || '#';
                    var source = n.source ? '<div class="news-source">' + n.source + '</div>' : '';
                    return '<a class="news-item" href="' + url + '" target="_blank">' +
                        img +
                        '<div class="news-text">' +
                        '<div class="news-headline">' + (n.title || '') + '</div>' +
                        source +
                        '</div></a>';
                }).join('');
            };

            // 初始渲染（如果 API 数据已就绪）
            window.renderProgressPanel();
            window.renderNewsPanel();
        });
    </script>
</footer>
</html>