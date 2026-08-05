(function (window) {
    var API_BASE = 'api.php';
    var KEY = 'nav_admin_settings';
    var cache = null;
    var apiAvailable = null; // null=unknown, true/false

    var defaults = {
        siteTitle: '液态玻璃导航栏',
        siteDesc: 'iOS 26 风格液态玻璃导航',
        siteFavicon: '',
        siteLogo: '',
        visitCount: 0,
        visitTrend: [],

        showDynamicIsland: true,
        showMusicList: true,
        showProgressPanel: true,
        showNews: true,
        showSearchTime: true,
        showDailyQuote: true,

        notices: [],
        homeCards: [],
        recommendCards: [],
        recommendBatches: [],
        feedback: [],
        musicPlaylist: [],
        musicAutoplay: false,
        newsItems: [],
        progressItems: []
    };

    function _read() {
        try {
            var raw = localStorage.getItem(KEY);
            var data = raw ? JSON.parse(raw) : {};
            return Object.assign({}, defaults, data);
        } catch (e) {
            return Object.assign({}, defaults);
        }
    }

    function _write(all) {
        localStorage.setItem(KEY, JSON.stringify(all));
    }

    function _apiCall(action, data, method) {
        method = method || 'GET';
        var url = API_BASE + '?action=' + action;
        var opts = {
            method: method,
            headers: { 'Content-Type': 'application/json' },
            credentials: 'include'
        };
        if (data) {
            opts.body = JSON.stringify(data);
        }
        return fetch(url, opts).then(function (res) { return res.json(); });
    }

    /**
     * 从后端加载数据到缓存
     */
    function loadFromServer() {
        return _apiCall('public_data', null, 'GET')
            .then(function (result) {
                if (result.code === 0 && result.data) {
                    apiAvailable = true;
                    var merged = Object.assign({}, defaults, result.data, _read());
                    // 将后端数据映射到本地存储格式
                    if (result.data.settings) {
                        merged.siteTitle = result.data.settings.siteTitle || merged.siteTitle;
                        merged.siteDesc = result.data.settings.siteDesc || merged.siteDesc;
                        merged.siteFavicon = result.data.settings.siteFavicon || merged.siteFavicon;
                        merged.showDynamicIsland = result.data.settings.showDynamicIsland !== undefined ? result.data.settings.showDynamicIsland : merged.showDynamicIsland;
                        merged.showMusicList = result.data.settings.showMusicList !== undefined ? result.data.settings.showMusicList : merged.showMusicList;
                        merged.showProgressPanel = result.data.settings.showProgressPanel !== undefined ? result.data.settings.showProgressPanel : merged.showProgressPanel;
                        merged.showNews = result.data.settings.showNews !== undefined ? result.data.settings.showNews : merged.showNews;
                        merged.showSearchTime = result.data.settings.showSearchTime !== undefined ? result.data.settings.showSearchTime : merged.showSearchTime;
                        merged.showDailyQuote = result.data.settings.showDailyQuote !== undefined ? result.data.settings.showDailyQuote : merged.showDailyQuote;
                    }
                    if (result.data.notices) merged.notices = result.data.notices;
                    if (result.data.musicPlaylist) merged.musicPlaylist = result.data.musicPlaylist;
                    if (result.data.newsItems) merged.newsItems = result.data.newsItems;
                    if (result.data.progressItems) merged.progressItems = result.data.progressItems;
                    if (result.data.homeCards) merged.homeCards = result.data.homeCards;
                    if (result.data.recommendCards) merged.recommendCards = result.data.recommendCards;
                    if (result.data.recommendBatches) merged.recommendBatches = result.data.recommendBatches;
                    cache = merged;
                    _write(merged);
                    return merged;
                }
                apiAvailable = false;
                cache = _read();
                return cache;
            })
            .catch(function () {
                apiAvailable = false;
                cache = _read();
                return cache;
            });
    }

    /**
     * 确保数据已加载
     */
    function ensureLoaded() {
        if (cache) return Promise.resolve(cache);
        return loadFromServer();
    }

    var NavSettings = {
        KEY: KEY,
        defaults: defaults,

        /**
         * 获取所有设置（同步，可能返回缓存数据）
         */
        getAll: function () {
            if (cache) return cache;
            return _read();
        },

        /**
         * 获取单个key
         */
        get: function (key) {
            return this.getAll()[key];
        },

        /**
         * 设置单个key（写入缓存+本地，后端同步）
         */
        set: function (key, val) {
            var all = this.getAll();
            all[key] = val;
            cache = all;
            _write(all);
            // 后端同步
            this._syncKey(key, val);
            return all;
        },

        /**
         * 批量设置
         */
        setAll: function (obj) {
            var all = Object.assign(this.getAll(), obj);
            cache = all;
            _write(all);
            // 同步到后端
            this._syncAll(obj);
            return all;
        },

        /**
         * 重置
         */
        reset: function () {
            apiAvailable = false; // 强制使用本地
            cache = Object.assign({}, defaults);
            _write(cache);
            return cache;
        },

        uid: function () {
            return Date.now().toString(36) + Math.random().toString(36).slice(2, 6);
        },

        dataUrl: function (file) {
            return new Promise(function (resolve, reject) {
                var r = new FileReader();
                r.onload = function () { resolve(r.result); };
                r.onerror = reject;
                r.readAsDataURL(file);
            });
        },

        /**
         * 记录访问量
         */
        recordVisit: function () {
            var all = this.getAll();
            all.visitCount = (all.visitCount || 0) + 1;
            var today = new Date().toLocaleDateString('zh-CN');
            var trend = all.visitTrend || [];
            var last = trend[trend.length - 1];
            if (last && last.date === today) { last.count = (last.count || 0) + 1; }
            else { trend.push({ date: today, count: 1 }); }
            if (trend.length > 30) trend = trend.slice(trend.length - 30);
            all.visitTrend = trend;
            cache = all;
            _write(all);
            // 同步到后端
            if (apiAvailable !== false) {
                _apiCall('visit').catch(function () {});
            }
            return all.visitCount;
        },

        /**
         * 获取访问趋势
         */
        getTrend: function (days) {
            days = days || 7;
            var trend = (this.getAll() || {}).visitTrend || [];
            var map = {};
            trend.forEach(function (it) { map[it.date] = it.count; });
            var arr = [], d = new Date();
            for (var i = days - 1; i >= 0; i--) {
                var dd = new Date(d); dd.setDate(d.getDate() - i);
                var key = dd.toLocaleDateString('zh-CN');
                arr.push({ date: key, count: map[key] || 0 });
            }
            return arr;
        },

        /**
         * 绑定文件上传按钮
         */
        bindFileField: function (inputId, accept, preview) {
            var input = document.getElementById(inputId);
            if (!input) return;
            var btn = document.querySelector('[data-target="' + inputId + '"]');
            if (btn) {
                if (!btn.querySelector('.up-text')) {
                    var label = '上传文件';
                    if (accept === 'image/*') label = '上传图片';
                    else if (accept === 'audio/*') label = '上传音频';
                    var sp = document.createElement('span');
                    sp.className = 'up-text'; sp.textContent = label;
                    btn.appendChild(sp);
                }
                btn.addEventListener('click', function () {
                    var fi = document.createElement('input');
                    fi.type = 'file'; fi.accept = accept || '*/*'; fi.style.display = 'none';
                    fi.addEventListener('change', function () {
                        var f = fi.files[0]; if (!f) return;
                        NavSettings.dataUrl(f).then(function (url) {
                            input.value = url;
                            if (preview) {
                                var prev = document.getElementById(inputId + 'Preview');
                                if (prev) { prev.src = url; prev.style.display = url ? '' : 'none'; }
                            }
                            else input.dispatchEvent(new Event('input'));
                        });
                        if (fi.parentNode) fi.parentNode.removeChild(fi);
                    });
                    document.body.appendChild(fi); fi.click();
                });
            }
            if (preview) {
                input.addEventListener('input', function () {
                    var prev = document.getElementById(inputId + 'Preview');
                    if (prev) { prev.src = input.value; prev.style.display = input.value ? '' : 'none'; }
                });
                var p = document.getElementById(inputId + 'Preview');
                if (p && input.value) { p.src = input.value; p.style.display = ''; }
            }
        },

        /**
         * 初始化 - 从后端加载数据
         */
        init: function () {
            return loadFromServer();
        },

        /**
         * 检查API是否可用
         */
        isApiAvailable: function () {
            return apiAvailable === true;
        },

        /**
         * 同步单个key到后端
         */
        _syncKey: function (key, val) {
            if (apiAvailable === false) return;
            var data = this.getAll();
            var postData = {};

            switch (key) {
                case 'notices':
                    var notices = val;
                    if (Array.isArray(notices) && notices.length > 0) {
                        var n = notices[0];
                        _apiCall('save_notice', {
                            title: n.title || '',
                            content: n.content || '',
                            url: n.url || ''
                        }, 'POST').catch(function () {});
                    } else {
                        _apiCall('delete_notice', {}, 'POST').catch(function () {});
                    }
                    break;

                case 'musicPlaylist':
                    if (Array.isArray(val)) {
                        // 全量同步：先获取后端列表，然后对比
                        _apiCall('get_music').then(function (res) {
                            if (res.code === 0) {
                                var existing = res.data || [];
                                var existingMap = {};
                                existing.forEach(function (m) { existingMap[m.id] = m; });
                                var newIds = {};
                                val.forEach(function (m) {
                                    newIds[m.id] = true;
                                    if (!existingMap[m.id]) {
                                        _apiCall('add_music', m, 'POST').catch(function () {});
                                    }
                                });
                                // 删除不在新列表中的
                                existing.forEach(function (m) {
                                    if (!newIds[m.id]) {
                                        _apiCall('delete_music', { id: m.id }, 'POST').catch(function () {});
                                    }
                                });
                            }
                        }).catch(function () {});
                    }
                    break;

                case 'newsItems':
                    if (Array.isArray(val)) {
                        _apiCall('get_news').then(function (res) {
                            if (res.code === 0) {
                                var existing = res.data || [];
                                var existingMap = {};
                                existing.forEach(function (n) { existingMap[n.id] = n; });
                                var newIds = {};
                                val.forEach(function (n) {
                                    newIds[n.id] = true;
                                    if (!existingMap[n.id]) {
                                        _apiCall('add_news', n, 'POST').catch(function () {});
                                    }
                                });
                                existing.forEach(function (n) {
                                    if (!newIds[n.id]) {
                                        _apiCall('delete_news', { id: n.id }, 'POST').catch(function () {});
                                    }
                                });
                            }
                        }).catch(function () {});
                    }
                    break;

                case 'progressItems':
                    if (Array.isArray(val)) {
                        _apiCall('get_progress').then(function (res) {
                            if (res.code === 0) {
                                var existing = res.data || [];
                                var existingMap = {};
                                existing.forEach(function (p) { existingMap[p.id] = p; });
                                var newIds = {};
                                val.forEach(function (p) {
                                    newIds[p.id] = true;
                                    if (!existingMap[p.id]) {
                                        _apiCall('add_progress', p, 'POST').catch(function () {});
                                    }
                                });
                                existing.forEach(function (p) {
                                    if (!newIds[p.id]) {
                                        _apiCall('delete_progress', { id: p.id }, 'POST').catch(function () {});
                                    }
                                });
                            }
                        }).catch(function () {});
                    }
                    break;

                case 'homeCards':
                    _apiCall('save_home_cards', { cards: val }, 'POST').catch(function () {});
                    break;

                case 'recommendCards':
                    _apiCall('save_recommend_cards', { cards: val }, 'POST').catch(function () {});
                    break;

                case 'recommendBatches':
                    _apiCall('save_recommend', { batches: val }, 'POST').catch(function () {});
                    break;

                case 'feedback':
                    // 反馈通常由前端提交，不在后台同步
                    break;

                case 'showMusicList':
                case 'showProgressPanel':
                case 'showNews':
                case 'showSearchTime':
                case 'showDailyQuote':
                case 'showDynamicIsland':
                    // 开关设置
                    var toggles = {};
                    toggles[key] = val;
                    _apiCall('save_toggles', toggles, 'POST').catch(function () {});
                    break;

                case 'siteTitle':
                case 'siteDesc':
                case 'siteFavicon':
                    _apiCall('save_settings', {
                        siteTitle: data.siteTitle,
                        siteDesc: data.siteDesc,
                        siteFavicon: data.siteFavicon
                    }, 'POST').catch(function () {});
                    break;
            }
        },

        /**
         * 批量同步设置到后端
         */
        _syncAll: function (obj) {
            if (apiAvailable === false) return;
            var self = this;
            Object.keys(obj).forEach(function (key) {
                self._syncKey(key, obj[key]);
            });
        }
    };

    window.NavSettings = NavSettings;

    // 自动初始化（异步加载）
    // 如果有DOMContentLoaded事件，等待它
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function () {
            NavSettings.init();
        });
    } else {
        NavSettings.init();
    }

})(window);
