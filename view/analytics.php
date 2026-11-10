<?php
// Componente compartilhado: escolha de cookies analíticos da TechLoja.
?>
<style>
  .tl-cookie-banner{position:fixed;z-index:9999;left:16px;right:16px;bottom:16px;max-width:780px;margin:auto;padding:18px 20px;background:#fff;color:#192337;border:1px solid #ccd4e5;border-radius:14px;box-shadow:0 8px 36px rgba(0,0,0,.18);font:14px/1.5 system-ui,sans-serif}
  .tl-cookie-banner p{margin:0 0 12px}
  .tl-cookie-banner a{color:#2556df;text-decoration:underline}
  .tl-cookie-actions{display:flex;flex-wrap:wrap;gap:10px}
  .tl-cookie-actions button,.tl-privacy-actions button{cursor:pointer;border:1px solid #2556df;border-radius:8px;padding:9px 14px;font:inherit}
  .tl-cookie-accept{background:#2556df;color:#fff}
  .tl-cookie-reject{background:#fff;color:#2556df}
</style>
<script>
(function () {
    'use strict';
    var key = 'techloja_ga4_choice';
    var measurementId = 'G-Z4V0PZP15H';
    var choice = null;
    var loaded = false;
    try { choice = localStorage.getItem(key); } catch (_) {}
    window.dataLayer = window.dataLayer || [];
    window.techlojaAnalyticsAllowed = false;
    window.gtag = function () {
        if (window.techlojaAnalyticsAllowed) {
            window.dataLayer.push(arguments);
        }
    };
    function enableAnalytics() {
        if (loaded) return;
        loaded = true;
        window.techlojaAnalyticsAllowed = true;
        window.gtag('js', new Date());
        window.gtag('config', measurementId);
        var script = document.createElement('script');
        script.async = true;
        script.src = 'https://www.googletagmanager.com/gtag/js?id=' + measurementId;
        document.head.appendChild(script);
    }
    function clearFirstPartyGaCookies() {
        document.cookie.split(';').forEach(function (item) {
            var name = item.trim().split('=')[0];
            if (name === '_ga' || name.indexOf('_ga_') === 0) {
                document.cookie = name + '=; Max-Age=0; Path=/; SameSite=Lax';
            }
        });
    }
    function refreshPrivacyStatus() {
        var status = document.getElementById('tl-analytics-status');
        if (status) status.textContent =
            choice === 'accepted' ? 'Ativadas' :
            choice === 'rejected' ? 'Recusadas' : 'Não definidas';
    }
    window.techlojaSetAnalyticsChoice = function (next) {
        if (next !== 'accepted' && next !== 'rejected') return;
        choice = next;
        try { localStorage.setItem(key, next); } catch (_) {}
        var banner = document.getElementById('tl-cookie-banner');
        if (banner) banner.remove();
        if (next === 'accepted') {
            enableAnalytics();
        } else {
            if (loaded) {
                window.gtag('consent', 'update', { analytics_storage: 'denied' });
                window.techlojaAnalyticsAllowed = false;
                clearFirstPartyGaCookies();
                window.location.reload();
                return;
            }
            clearFirstPartyGaCookies();
        }
        refreshPrivacyStatus();
    };
    if (choice === 'accepted') enableAnalytics();
    document.addEventListener('DOMContentLoaded', function () {
        refreshPrivacyStatus();
        if (choice === 'accepted' || choice === 'rejected') return;
        var banner = document.createElement('section');
        banner.id = 'tl-cookie-banner';
        banner.className = 'tl-cookie-banner';
        banner.setAttribute('aria-label', 'Preferências de cookies');
        var info = document.createElement('p');
        info.textContent = 'A TechLoja usa um cookie necessário para manter a sessão. Com sua autorização, também utiliza Google Analytics 4 para medir visitas e compras simuladas. Recusar não afeta o funcionamento da loja. ';
        var link = document.createElement('a');
        link.href = <?= json_encode(getenv('VERCEL') === '1' ? '/privacidade.php' : '../privacidade.php') ?>;
        link.textContent = 'Política de privacidade';
        info.appendChild(link);
        var actions = document.createElement('div');
        actions.className = 'tl-cookie-actions';
        [['Recusar análises', 'rejected', 'tl-cookie-reject'], ['Aceitar análises', 'accepted', 'tl-cookie-accept']].forEach(function (item) {
            var button = document.createElement('button');
            button.type = 'button';
            button.textContent = item[0];
            button.className = item[2];
            button.addEventListener('click', function () { window.techlojaSetAnalyticsChoice(item[1]); });
            actions.appendChild(button);
        });
        banner.appendChild(info);
        banner.appendChild(actions);
        document.body.appendChild(banner);
    });
})();
</script>
