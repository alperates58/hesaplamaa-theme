(function() {
'use strict';

document.addEventListener('DOMContentLoaded', function() {

    /* ── Mobil Nav Toggle ── */
    var mobileBtn = document.getElementById('mobile-toggle');
    var mobileNav = document.getElementById('mobile-nav');

    function resetMobileButton() {
        if (!mobileBtn) return;
        mobileBtn.setAttribute('aria-expanded', 'false');
        mobileBtn.querySelectorAll('span').forEach(function(s) {
            s.style.transform = '';
            s.style.opacity   = '';
        });
    }

    function closeMobileNav() {
        if (mobileNav) {
            mobileNav.classList.remove('is-open');
            mobileNav.querySelectorAll('.menu-item-has-children.is-open').forEach(function(li) {
                li.classList.remove('is-open');
            });
        }
        document.body.classList.remove('nav-open');
        resetMobileButton();
    }

    if (mobileBtn && mobileNav) {
        mobileBtn.addEventListener('click', function() {
            var open = mobileNav.classList.toggle('is-open');
            mobileBtn.setAttribute('aria-expanded', open);
            document.body.classList.toggle('nav-open', open);
            // Hamburger animasyonu
            var spans = mobileBtn.querySelectorAll('span');
            if (open) {
                spans[0].style.transform = 'rotate(45deg) translate(5px,5px)';
                spans[1].style.opacity   = '0';
                spans[2].style.transform = 'rotate(-45deg) translate(5px,-5px)';
            } else {
                spans[0].style.transform = '';
                spans[1].style.opacity   = '';
                spans[2].style.transform = '';
            }
        });

        // Alt menü aç/kapat
        mobileNav.querySelectorAll('.menu-item-has-children > a').forEach(function(a) {
            a.addEventListener('click', function(e) {
                e.preventDefault();
                var li = a.parentElement;
                li.classList.toggle('is-open');
            });
        });
    }

    /* ── Resize: kapat ── */
    window.addEventListener('resize', function() {
        if (window.innerWidth > 900 && mobileNav) {
            closeMobileNav();
        }
    });

    /* ── Search Toggle ── */
    var searchBtn = document.getElementById('search-toggle');
    var searchBar = document.getElementById('header-search');
    if (searchBtn && searchBar) {
        searchBtn.addEventListener('click', function() {
            var open = searchBar.classList.toggle('is-open');
            searchBtn.setAttribute('aria-expanded', open);
            if (open) {
                closeMobileNav();
                var inp = searchBar.querySelector('input');
                if (inp) setTimeout(function(){ inp.focus(); }, 60);
            }
        });
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                searchBar.classList.remove('is-open');
                searchBtn.setAttribute('aria-expanded', 'false');
                closeMobileNav();
            }
        });
    }

    /* ── Header gizle scroll'da ── */
    var header = document.querySelector('.site-header.is-sticky.can-hide-scroll');
    var lastY  = window.scrollY;
    if (header) {
        header.style.transition = 'transform .3s cubic-bezier(.4,0,.2,1)';
        window.addEventListener('scroll', function() {
            var y = window.scrollY;
            if (y > lastY && y > 80) {
                header.style.transform = 'translateY(-100%)';
            } else {
                header.style.transform = '';
            }
            lastY = y;
        }, { passive: true });
    }

    /* ── TOC Scroll Spy ── */
    (function() {
        var tocLinks = document.querySelectorAll('.toc-widget .toc-list a');
        if (!tocLinks.length) return;

        var headings = [];
        tocLinks.forEach(function(link) {
            var id = link.getAttribute('href').slice(1);
            var el = document.getElementById(id);
            if (el) headings.push({ el: el, link: link });
        });
        if (!headings.length) return;

        function updateActive() {
            var scrollY = window.scrollY + 120;
            var active  = null;
            headings.forEach(function(h) {
                if (h.el.offsetTop <= scrollY) active = h;
            });
            tocLinks.forEach(function(l) {
                l.parentElement.classList.remove('toc-active');
            });
            if (active) active.link.parentElement.classList.add('toc-active');
        }

        window.addEventListener('scroll', updateActive, { passive: true });
        updateActive();
    })();

    /* Sonuc paylaşımı */
    var sharePanel = document.querySelector('[data-share-panel]');
    if (sharePanel) {
        var shareStatus = sharePanel.querySelector('[data-share-status]');
        var printUrl = sharePanel.querySelector('[data-share-print-url]');
        var resultSelectors = [
            '.result-field',
            '.result-box',
            '.sonuc',
            '.sonuc-kutusu',
            '.sonuc-alani',
            '.calculator-result',
            '.calc-result',
            '[id*="sonuc" i]',
            '[class*="sonuc" i]',
            '[id*="result" i]',
            '[class*="result" i]',
            'output'
        ];

        function setShareStatus(message) {
            if (!shareStatus) return;
            shareStatus.textContent = message || '';
            shareStatus.classList.toggle('is-visible', Boolean(message));
        }

        function getResultText() {
            var entry = document.querySelector('.entry-inner');
            if (!entry) return '';

            var candidates = Array.prototype.slice.call(entry.querySelectorAll(resultSelectors.join(',')));
            var texts = candidates.map(function(el) {
                if (!el || sharePanel.contains(el)) return '';
                if (el.offsetParent === null && el.tagName !== 'OUTPUT') return '';

                var value = '';
                if ('value' in el && el.value) {
                    value = el.value;
                } else {
                    value = el.innerText || el.textContent || '';
                }

                return value.replace(/\s+/g, ' ').trim();
            }).filter(function(text) {
                return text.length > 1 && !/hesapla|payla[sş]|yazd[ıi]r|pdf/i.test(text);
            });

            return texts[0] || '';
        }

        function getShareData() {
            var result = getDetailedResultText();
            var summary = getResultSummary(result);
            var title = (document.querySelector('.single-title') || document.querySelector('h1') || document).textContent || document.title;
            title = title.replace(/\s+/g, ' ').trim();
            var url = new URL(window.location.href);
            if (summary) {
                url.searchParams.set('sonuc', summary);
            }
            url.hash = 'result-share-title';

            var text = result ? title + '\nSonuç: ' + result + '\n' + url.href : title + '\n' + url.href;
            if (result) {
                text = title + '\n\nSonuc:\n' + result + '\n\nDetaylar ve tekrar hesaplama:\n' + url.href;
            }
            return {
                title: title,
                summary: summary,
                result: result,
                url: url.href,
                text: text
            };
        }

        function normalizeDetailedResultText(text) {
            return (text || '')
                .replace(/\r/g, '')
                .split('\n')
                .map(function(line) {
                    return line.replace(/\s+/g, ' ').trim();
                })
                .filter(Boolean)
                .join('\n')
                .replace(/\n{3,}/g, '\n\n')
                .trim();
        }

        function getDetailedResultText() {
            var entry = document.querySelector('.entry-inner');
            if (!entry) return getResultText();

            var candidates = Array.prototype.slice.call(entry.querySelectorAll(resultSelectors.join(',')));
            var expanded = [];

            candidates.forEach(function(el) {
                var node = el;
                for (var i = 0; i < 3 && node && node !== entry; i++) {
                    expanded.push(node);
                    node = node.parentElement;
                }
            });

            var texts = expanded.filter(function(el, index, arr) {
                return el && arr.indexOf(el) === index && !sharePanel.contains(el) && Boolean(el.offsetWidth || el.offsetHeight || el.getClientRects().length);
            }).map(function(el) {
                var value = ('value' in el && el.value) ? el.value : (el.innerText || el.textContent || '');
                return normalizeDetailedResultText(value);
            }).filter(function(text) {
                return text.length > 1 && !/^(hesapla|payla[sÅŸ]|yazd[Ä±i]r|pdf|yeniden hesapla)$/i.test(text);
            });

            texts.sort(function(a, b) {
                return b.length - a.length;
            });

            return texts[0] || getResultText();
        }

        function getResultSummary(result) {
            var firstLine = (result || '').split('\n').filter(Boolean)[0] || '';
            return firstLine.slice(0, 120);
        }

        function updatePrintUrl(data) {
            if (!printUrl) return;
            printUrl.textContent = data.result ? 'Sonuç bağlantısı: ' + data.url : data.url;
        }

        function copyShareText(data) {
            if (!navigator.clipboard) return false;
            navigator.clipboard.writeText(data.text).then(function() {
                setShareStatus((window.hthemeShare && window.hthemeShare.copied) || 'Sonuç kopyalandı.');
            });
            return true;
        }

        function openShareWindow(url) {
            window.open(url, '_blank', 'noopener,noreferrer,width=720,height=640');
        }

        sharePanel.addEventListener('click', function(e) {
            var btn = e.target.closest('[data-share-action]');
            if (!btn) return;

            var action = btn.getAttribute('data-share-action');
            var data = getShareData();
            updatePrintUrl(data);
            var encodedText = encodeURIComponent(data.text);
            var encodedUrl = encodeURIComponent(data.url);
            var encodedTitle = encodeURIComponent(data.title);

            if (!data.result && ['recalculate', 'print', 'pdf'].indexOf(action) === -1) {
                setShareStatus((window.hthemeShare && window.hthemeShare.noResult) || 'Paylaşılacak bir sonuç bulunamadı.');
                return;
            }

            setShareStatus('');

            if (action === 'facebook') {
                openShareWindow('https://www.facebook.com/sharer/sharer.php?u=' + encodedUrl + '&quote=' + encodedText);
                return;
            }

            if (action === 'x') {
                openShareWindow('https://twitter.com/intent/tweet?text=' + encodedText);
                return;
            }

            if (action === 'telegram') {
                openShareWindow('https://t.me/share/url?url=' + encodedUrl + '&text=' + encodedText);
                return;
            }

            if (action === 'whatsapp') {
                openShareWindow('https://api.whatsapp.com/send?text=' + encodedText);
                return;
            }

            if (action === 'email') {
                window.location.href = 'mailto:?subject=' + encodedTitle + '&body=' + encodedText;
                return;
            }

            if (action === 'instagram') {
                if (navigator.share) {
                    navigator.share({ title: data.title, text: data.text, url: data.url }).catch(function(){});
                } else if (!copyShareText(data)) {
                    setShareStatus('Instagram için sonucu kopyalayın: ' + data.text);
                }
                return;
            }

            if (action === 'pdf' || action === 'print') {
                window.print();
                return;
            }

            if (action === 'recalculate') {
                var firstInput = document.querySelector('.entry-inner input, .entry-inner select, .entry-inner textarea');
                if (firstInput) {
                    firstInput.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    setTimeout(function(){ firstInput.focus(); }, 350);
                } else {
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                }
            }
        });

        var sharedResult = new URLSearchParams(window.location.search).get('sonuc');
        if (sharedResult) {
            setShareStatus('Paylaşılan sonuç: ' + sharedResult);
        }
    }

    /* ── Category page search filter ── */
    var catSearch = document.getElementById('cat-search');
    if (catSearch) {
        catSearch.addEventListener('input', function() {
            var q = this.value.toLowerCase().trim();
            document.querySelectorAll('#cat-grid [data-name]').forEach(function(el) {
                el.style.display = (!q || el.dataset.name.includes(q)) ? '' : 'none';
            });
        });
    }

});
})();
