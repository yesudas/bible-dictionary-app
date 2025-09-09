async function copyQuizSection() {
  const quizContent = document.querySelector('.quiz-section');
  const copyBtn = document.getElementById('copyBtn');


  if (!quizContent) {
    alert("No quiz section found!");
    return;
  }

  // Helper: take an element's innerHTML, replace <br> with \n, return textContent trimmed.
  function elementTextWithBr(el) {
    if (!el) return '';
    const html = (el.innerHTML || '').replace(/<br\s*\/?>/gi, '\n');
    const tmp = document.createElement('div');
    tmp.innerHTML = html;
    // replace NBSP with normal space, collapse weird whitespace later by trim
    return (tmp.textContent || '').replace(/\u00A0/g, ' ').trim();
  }

  const blocks = [];

  // 1) Try DOM h1 first
  const h1 = quizContent.querySelector('h1');
  let headingText = elementTextWithBr(h1);

  // 2) If headingText empty, try AngularJS scope fallback (for AngularJS apps)
  if ((!headingText || headingText.length === 0) && window.angular && typeof angular.element === 'function') {
    try {
      const ngEl = angular.element(quizContent);
      const scope = (ngEl && (ngEl.scope ? ngEl.scope() : null)) || (ngEl && ngEl.isolateScope && ngEl.isolateScope());
      if (scope) {
        if (scope.Word) {
          // Word might be object {word: "..."} or a string
          if (typeof scope.Word === 'string') headingText = (scope.Word || '').trim();
          else if (scope.Word.word) headingText = (scope.Word.word || '').toString().trim();
        } else if (scope.$parent && scope.$parent.Word) {
          // try parent scope if Word is higher up
          const pw = scope.$parent.Word;
          headingText = (typeof pw === 'string' ? pw : (pw && pw.word) ? pw.word : '') .toString().trim();
        }
      }
    } catch (e) {
      console.warn('Angular scope read failed:', e);
    }
  }

  if (headingText) blocks.push(headingText);

  // 3) Collect paragraphs (and convert internal <br> to newlines)
  quizContent.querySelectorAll('p').forEach(p => {
    const t = elementTextWithBr(p);
    if (t) blocks.push(t);
  });

  // 4) If still nothing found, fallback to grabbing visible text nodes
  if (blocks.length === 0) {
    const walker = document.createTreeWalker(quizContent, NodeFilter.SHOW_TEXT, {
      acceptNode: function(node) {
        if (!node.nodeValue || !node.nodeValue.trim()) return NodeFilter.FILTER_REJECT;
        const parentTag = node.parentNode && node.parentNode.nodeName && node.parentNode.nodeName.toLowerCase();
        if (['script', 'style', 'noscript'].includes(parentTag)) return NodeFilter.FILTER_REJECT;
        return NodeFilter.FILTER_ACCEPT;
      }
    });
    let node;
    let acc = '';
    while (node = walker.nextNode()) {
      acc += node.nodeValue.replace(/\s+/g, ' ') + ' ';
    }
    if (acc.trim()) blocks.push(acc.trim());
  }

  // Join blocks with double newlines so every h1/p appears on a new line + blank line after
  const finalText = blocks.join('\n\n');

  // Copy to clipboard (modern API + fallback)
  try {
    if (navigator.clipboard && navigator.clipboard.writeText) {
      await navigator.clipboard.writeText(finalText);
      //alert('Quiz content copied to clipboard!');
      
      // ✅ Success feedback
    const originalText = copyBtn.textContent;
    copyBtn.textContent = "Copied";
    copyBtn.classList.remove("btn-primary");
    copyBtn.classList.add("btn-success");

    setTimeout(() => {
      copyBtn.textContent = originalText;
      copyBtn.classList.remove("btn-success");
      copyBtn.classList.add("btn-primary");
    }, 2000);
      
      return;
    }
    throw new Error('clipboard API not available');
  } catch (err) {
    console.warn('navigator.clipboard failed, trying fallback: ', err);
    // fallback: hidden textarea + execCommand
    const ta = document.createElement('textarea');
    ta.value = finalText;
    ta.setAttribute('readonly', '');
    ta.style.position = 'fixed';
    ta.style.left = '-9999px';
    document.body.appendChild(ta);
    ta.select();
    ta.setSelectionRange(0, ta.value.length); // for iOS
    try {
      const ok = document.execCommand('copy');
      document.body.removeChild(ta);
      if (ok) { 
          //alert('Quiz content copied to clipboard (fallback)');
          // ✅ Success feedback
    const originalText = copyBtn.textContent;
    copyBtn.textContent = "Copied";
    copyBtn.classList.remove("btn-primary");
    copyBtn.classList.add("btn-success");

    setTimeout(() => {
      copyBtn.textContent = originalText;
      copyBtn.classList.remove("btn-success");
      copyBtn.classList.add("btn-primary");
    }, 2000);
          return; 
      }
      throw new Error('execCommand returned false');
    } catch (e) {
      document.body.removeChild(ta);
      console.error('Fallback copy failed:', e);
      alert('Copy failed. Please select and copy manually.');
    }
  }
}