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
