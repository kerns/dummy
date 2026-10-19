import hljs from './vendor/highlight/core.min.js';
import php from './vendor/highlight/php.min.js';
import xml from './vendor/highlight/xml.min.js';
import phpTemplate from './vendor/highlight/php-template.min.js';

hljs.registerLanguage('php', php);
hljs.registerLanguage('xml', xml);
hljs.registerLanguage('php-template', phpTemplate);

for (const code of document.querySelectorAll('pre code')) {
  hljs.highlightElement(code);
}

for (const button of document.querySelectorAll('[data-copy-code]')) {
  button.addEventListener('click', async () => {
    const code = button.closest('.code-snippet').querySelector('pre code');
    try {
      await navigator.clipboard.writeText(code.textContent);
      button.textContent = 'Copied';
    } catch {
      const range = document.createRange();
      range.selectNodeContents(code);
      const selection = window.getSelection();
      selection.removeAllRanges();
      selection.addRange(range);
      button.textContent = 'Code selected';
    }
    window.setTimeout(() => { button.textContent = 'Copy'; }, 1800);
  });
}

const navigation = document.querySelector('.page-nav');
const sections = [...navigation.querySelectorAll('a[href^="#"]')]
  .map(link => ({ link, target: document.getElementById(link.hash.slice(1)) }))
  .filter(section => section.target);

let activeSection;
let clippedAnchor;
let anchorRestPosition;
let anchorRestTimer;
let updatePending = false;

function updateNavigation() {
  updatePending = false;
  const activationLine = parseFloat(getComputedStyle(document.documentElement).scrollPaddingTop) + 1;
  let current = sections[0];
  for (const section of sections) {
    if (section.target.getBoundingClientRect().top > activationLine) break;
    current = section;
  }
  // Short final sections cannot always reach the top of the viewport.
  if (window.scrollY + window.innerHeight >= document.documentElement.scrollHeight - 2) {
    current = sections.at(-1);
  }
  if (clippedAnchor) current = clippedAnchor;
  if (!current || current === activeSection) return;
  activeSection?.link.removeAttribute('aria-current');
  current.link.setAttribute('aria-current', 'location');
  activeSection = current;

  // Keep the current item visible when the menu needs to scroll on shorter screens.
  if (navigation.offsetParent !== null) {
    const menu = navigation.getBoundingClientRect();
    const item = current.link.getBoundingClientRect();
    if (item.top < menu.top) navigation.scrollTop -= menu.top - item.top;
    if (item.bottom > menu.bottom) navigation.scrollTop += item.bottom - menu.bottom;
  }
}

function scheduleNavigationUpdate() {
  if (updatePending) return;
  updatePending = true;
  window.requestAnimationFrame(updateNavigation);
}

// A near-bottom anchor may be unable to reach the activation line on a tall screen.
// Honor an explicit jump until the reader resumes scrolling through the document.
function selectAnchor(section) {
  const maxScroll = Math.max(0, document.documentElement.scrollHeight - window.innerHeight);
  const offset = parseFloat(getComputedStyle(document.documentElement).scrollPaddingTop);
  clippedAnchor = section && section.target.getBoundingClientRect().top + window.scrollY > maxScroll + offset
    ? section : undefined;
  anchorRestPosition = undefined;
  window.clearTimeout(anchorRestTimer);
  if (clippedAnchor) recordAnchorRest();
  scheduleNavigationUpdate();
}

function recordAnchorRest() {
  window.clearTimeout(anchorRestTimer);
  anchorRestTimer = window.setTimeout(() => { anchorRestPosition = window.scrollY; }, 150);
}

function followDocument() {
  if (!clippedAnchor) return;
  clippedAnchor = undefined;
  anchorRestPosition = undefined;
  window.clearTimeout(anchorRestTimer);
  scheduleNavigationUpdate();
}

for (const section of sections) {
  section.link.addEventListener('click', event => {
    if (event.button === 0 && !event.metaKey && !event.ctrlKey && !event.shiftKey && !event.altKey) {
      selectAnchor(section);
    }
  });
}
const followHash = () => selectAnchor(sections.find(section => section.target.id === location.hash.slice(1)));

window.addEventListener('scroll', () => {
  if (clippedAnchor) {
    if (anchorRestPosition !== undefined && Math.abs(window.scrollY - anchorRestPosition) > 2) followDocument();
    else recordAnchorRest();
  }
  scheduleNavigationUpdate();
}, { passive: true });
window.addEventListener('resize', () => { followDocument(); scheduleNavigationUpdate(); });
window.addEventListener('hashchange', followHash);
window.addEventListener('pageshow', followHash);
window.addEventListener('wheel', followDocument, { passive: true });
window.addEventListener('touchmove', followDocument, { passive: true });
window.addEventListener('keydown', event => {
  if (['ArrowUp', 'ArrowDown', 'PageUp', 'PageDown', 'Home', 'End', ' '].includes(event.key)) followDocument();
});
window.addEventListener('pointerdown', event => {
  if (event.target === document.documentElement) followDocument();
});
new ResizeObserver(scheduleNavigationUpdate).observe(document.getElementById('main'));
followHash();
updateNavigation();
