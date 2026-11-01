{{-- Reusable right-side payment drawer for all public donation links. --}}
<style>
.sz-unified-pay-overlay{position:fixed;inset:0;z-index:9998;background:rgba(8,28,49,.58);opacity:0;visibility:hidden;transition:opacity .25s ease,visibility .25s ease;backdrop-filter:blur(2px)}
.sz-unified-pay-panel{position:fixed;right:0;top:0;bottom:0;width:min(560px,100vw);z-index:9999;background:#fff;box-shadow:-16px 0 48px rgba(7,29,51,.25);transform:translateX(105%);visibility:hidden;transition:transform .28s ease,visibility .28s ease;direction:rtl}
.sz-unified-pay-overlay.is-open{opacity:1;visibility:visible}.sz-unified-pay-panel.is-open{transform:translateX(0);visibility:visible}
.sz-unified-pay-frame{display:block;width:100%;height:100%;border:0;background:white}.sz-unified-pay-loading{position:absolute;inset:0;display:grid;place-items:center;background:#f5f9fd;color:#10518f;font:700 15px Cairo,Tahoma,sans-serif;pointer-events:none}.sz-unified-pay-panel.is-loaded .sz-unified-pay-loading{display:none}
@media(prefers-reduced-motion:reduce){.sz-unified-pay-overlay,.sz-unified-pay-panel{transition:none}}
</style>
<div id="sz-unified-pay-overlay" class="sz-unified-pay-overlay" aria-hidden="true"></div>
<aside id="sz-unified-pay-panel" class="sz-unified-pay-panel" role="dialog" aria-modal="true" aria-label="الدفع بالحوالة المصرفية" aria-hidden="true" tabindex="-1"><div class="sz-unified-pay-loading" aria-live="polite">جاري فتح نموذج التبرع…</div><iframe id="sz-unified-pay-frame" class="sz-unified-pay-frame" title="نموذج الدفع بالحوالة المصرفية"></iframe></aside>
<script>
(function(){
 const overlay=document.getElementById('sz-unified-pay-overlay'),panel=document.getElementById('sz-unified-pay-panel'),frame=document.getElementById('sz-unified-pay-frame');
 if(!overlay||!panel||!frame)return;
 let previousFocus=null,previousOverflow='',opening=false;
 function close(){if(!opening)return;opening=false;panel.classList.remove('is-open','is-loaded');overlay.classList.remove('is-open');panel.setAttribute('aria-hidden','true');overlay.setAttribute('aria-hidden','true');document.body.style.overflow=previousOverflow;frame.removeAttribute('src');if(previousFocus&&typeof previousFocus.focus==='function')previousFocus.focus()}
 function openPayment(url){previousFocus=document.activeElement;previousOverflow=document.body.style.overflow;opening=true;const target=new URL(url,window.location.href);target.searchParams.set('embedded','1');panel.classList.remove('is-loaded');frame.src=target.toString();panel.classList.add('is-open');overlay.classList.add('is-open');panel.setAttribute('aria-hidden','false');overlay.setAttribute('aria-hidden','false');document.body.style.overflow='hidden';panel.focus()}
 document.addEventListener('click',function(event){const anchor=event.target.closest('a[href]');if(!anchor||event.defaultPrevented||event.button!==0||event.ctrlKey||event.metaKey||event.altKey||event.shiftKey||anchor.hasAttribute('download')||anchor.target==='_blank')return;let url;try{url=new URL(anchor.href,location.href)}catch(e){return}if(url.origin!==location.origin||url.pathname!=='/donate/bank-transfer'||!url.searchParams.has('type')||!url.searchParams.has('id'))return;event.preventDefault();openPayment(url.href)});
 overlay.addEventListener('click',close);
 document.addEventListener('keydown',function(event){if(!opening)return;if(event.key==='Escape'){event.preventDefault();close()}else if(event.key==='Tab'){/* Keep keyboard navigation inside the embedded payment form. */if(document.activeElement===panel){event.preventDefault();frame.focus()}}});
 frame.addEventListener('load',function(){if(opening)panel.classList.add('is-loaded')});
 window.addEventListener('message',function(event){if(event.origin===location.origin&&event.source===frame.contentWindow&&event.data&&event.data.type==='sirte-close-payment')close()});
})();
</script>
