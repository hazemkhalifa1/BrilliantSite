(function () {
  var API_BASE = window.BS_API_BASE || '/api';
  function getToken() {
    var m = document.cookie.match(/(?:^|; )_be_token=([^;]+)/);
    return m ? decodeURIComponent(m[1]) : null;
  }
  function upload(spec, targetId) {
    var token = getToken();
    var infl = document.createElement('input');
    infl.type = 'file';
    infl.accept = spec.indexOf('image') === 0 ? 'image/*' : '.pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.zip';
    infl.onchange = function () {
      if (!infl.files || !infl.files[0]) return;
      var fd = new FormData();
      fd.append('file', infl.files[0]);
      fetch(API_BASE + '/upload/' + spec, {
        method: 'POST',
        headers: token ? { Authorization: 'Bearer ' + token } : {},
        body: fd
      }).then(function (r) {
        return r.json().catch(function () {
          throw new Error('Server responded with status ' + r.status + ' (invalid response).');
        });
      }).then(function (b) {
        if (b && b.success) {
          document.getElementById(targetId).value = b.data;
          document.getElementById(targetId).dispatchEvent(new Event('change'));
        } else { alert((b && b.message) || 'Upload failed'); }
      }).catch(function (err) { alert('Upload failed: ' + ((err && err.message) || 'network error')); });
    };
    infl.click();
  }
  document.addEventListener('click', function (e) {
    var btn = e.target.closest('[data-upload]');
    if (btn) { e.preventDefault(); upload(btn.getAttribute('data-upload'), btn.getAttribute('data-target')); }
  });

  function splitSingleHeaderText(text, cols) {
    text = (text || '').trim();
    if (!text || cols <= 1) return null;
    // Header 1Header 2 - handle both Header and header lower
    if (/header/i.test(text)) {
      var idx = text.toLowerCase().indexOf('header', 1);
      // find second occurrence case-insensitive
      var lower = text.toLowerCase();
      var first = lower.indexOf('header');
      var second = lower.indexOf('header', first + 1);
      if (second > 0 && cols === 2) return [text.slice(0, second).trim(), text.slice(second).trim()];
      var parts = text.split(/(?=Header)/).map(function(s){return s.trim();}).filter(function(s){return s;});
      if (parts.length === cols) return parts;
      parts = text.split(/(?=header)/i).map(function(s){return s.trim();}).filter(function(s){return s;});
      if (parts.length === cols) return parts;
    }
    // Arabic ة + letter without space
    var tmp = text.replace(/\u0629(?=[\u0600-\u06FF])/g, '\u0629\t');
    if (tmp.indexOf('\t') !== -1) {
      var p = tmp.split('\t').map(function(s){return s.trim();}).filter(function(s){return s;});
      if (p.length === cols) return p;
    }
    // digit + letter  e.g. "1Header" -> "1 Header"
    tmp = text.replace(/([0-9])(?=[A-Za-z\u0600-\u06FF])/g, '$1\t');
    if (tmp.indexOf('\t') !== -1) {
      var p2 = tmp.split('\t').map(function(s){return s.trim();}).filter(function(s){return s;});
      if (p2.length === cols) return p2;
    }
    // Fallback: split by middle space nearest to half
    var words = text.split(/\s+/).filter(function(w){return w;});
    if (words.length >= cols * 2) {
      var perCol = Math.ceil(words.length / cols);
      var res = [];
      for (var i=0;i<cols;i++) res.push(words.slice(i*perCol,(i+1)*perCol).join(' '));
      res = res.map(function(s){return s.trim();}).filter(function(s){return s;});
      if (res.length === cols) return res;
    }
    if (words.length === cols) return words;
    return null;
  }

  // Quill rich editors (render the editor inside a holder div, sync to the textarea on submit)
  var quills = [];
  document.querySelectorAll('textarea[data-quill]').forEach(function (ta) {
    if (typeof Quill === 'undefined') return;
    var holder = document.createElement('div');
    holder.className = 'ql-holder';
    ta.parentNode.insertBefore(holder, ta);
    ta.style.display = 'none';
    try {
      var q = new Quill(holder, {
        theme: 'snow',
        modules: { toolbar: [[{ header: [2, 3, false] }], ['bold', 'italic'], [{ list: 'ordered' }, { list: 'bullet' }], ['link']] }
      });
      var initial = ta.value || '';
      // If the stored value is Markdown (e.g. "## Heading" / "**bold**"), the PHP already
      // converts it to HTML via markdown_to_html(); as fallback handle raw Markdown here.
      if (initial && !/<[a-z][\s\S]*>/i.test(initial) && /(^|\n)\s{0,3}(?:#{2,3}\s|\*\*|__|\d+\.\s|[-*+]\s|---)/.test(initial)) {
        // Minimal client-side fallback: convert basic Markdown to HTML so toolbar shows formatting.
        initial = initial
          .replace(/^###\s+(.*)$/gm, '<h3>$1</h3>')
          .replace(/^##\s+(.*)$/gm, '<h2>$1</h2>')
          .replace(/\*\*(.+?)\*\*/g, '<strong>$1</strong>')
          .replace(/__(.+?)__/g, '<strong>$1</strong>')
          .replace(/^\s*[-*+]\s+(.*)$/gm, '<ul><li>$1</li></ul>')
          .replace(/^\s*\d+\.\s+(.*)$/gm, '<ol><li>$1</li></ol>')
          .replace(/^---\s*$/gm, '<hr>');
        // Merge consecutive list wrappers
        initial = initial.replace(/<\/ul>\s*<ul>/g, '').replace(/<\/ol>\s*<ol>/g, '');
        // Wrap remaining plain lines in <p>
        initial = initial.split(/\n\s*\n/).map(function(block){
          var t = block.trim();
          if (!t) return '';
          if (/^<(h[2-3]|ul|ol|hr|blockquote|pre)/i.test(t)) return t;
          return '<p>' + t.replace(/\n/g, '<br>') + '</p>';
        }).join('');
      }
      q.root.innerHTML = initial;
      function fixDisplayTables(){
        var tables = q.root.querySelectorAll('table');
        tables.forEach(function(table){
          var rows = table.querySelectorAll('tr');
          if (rows.length < 2) return;
          var maxCols = 0;
          rows.forEach(function(tr){ maxCols = Math.max(maxCols, tr.querySelectorAll('td,th').length); });
          if (maxCols < 2) return;
          var firstTr = rows[0];
          var cells = firstTr.querySelectorAll('td,th');
          if (cells.length === 1 && maxCols > 1) {
            var raw = (cells[0].innerText || cells[0].textContent || '').trim();
            var parts = splitSingleHeaderText(raw, maxCols);
            if (parts) {
              var isRtl = document.documentElement.dir === 'rtl' || document.documentElement.lang === 'ar';
              var align = isRtl ? 'right' : 'left';
              firstTr.innerHTML = '';
              parts.forEach(function(txt){
                var th = document.createElement('th');
                th.textContent = txt;
                th.style.background = '#0f1e6c';
                th.style.color = '#fff';
                th.style.padding = '10px 14px';
                th.style.textAlign = align;
                th.style.border = '1px solid #1e3a8a';
                th.style.wordBreak = 'break-word';
                th.style.verticalAlign = 'middle';
                th.style.fontWeight = '700';
                firstTr.appendChild(th);
              });
              table.style.width='100%'; table.style.borderCollapse='collapse'; table.style.tableLayout='fixed';
            }
          } else {
            firstTr.querySelectorAll('th').forEach(function(th){
              th.style.background='#0f1e6c'; th.style.color='#fff'; th.style.border='1px solid #1e3a8a';
            });
            firstTr.querySelectorAll('td').forEach(function(td){
              // if first row is td but should be th (no thead yet), style it as th for preview
              if (td.parentElement === firstTr && firstTr.querySelectorAll('td').length === maxCols) {
                td.style.background='#0f1e6c'; td.style.color='#fff'; td.style.border='1px solid #1e3a8a';
              }
            });
          }
          // ensure all tables have fixed layout
          table.style.width='100%'; table.style.borderCollapse='collapse'; table.style.tableLayout='fixed';
        });
      }
      fixDisplayTables();
      setTimeout(fixDisplayTables, 100);
      quills.push({ ta: ta, q: q, fixDisplay: fixDisplayTables });
      // --- Table support for blog content fields (content / contentAr) ---
      var isBlogTableField = (ta.id === 'f_content' || ta.id === 'f_contentAr' || ta.name === 'content' || ta.name === 'contentAr');
      if (isBlogTableField) {
        // Add Table button to toolbar
        var toolbar = holder.parentNode.querySelector('.ql-toolbar');
        if (toolbar) {
          var tableBtn = document.createElement('button');
          tableBtn.type = 'button';
          tableBtn.className = 'ql-table';
          tableBtn.innerHTML = 'Table';
          tableBtn.title = 'Insert Table';
          tableBtn.style.cssText = 'width:auto;padding:0 8px;font-size:11px;font-weight:600;border:1px solid #e2e8f0;margin-left:8px;';
          tableBtn.addEventListener('click', function() {
            var rows = parseInt(prompt('Rows (including header):', '3') || '3', 10);
            var cols = parseInt(prompt('Columns:', '2') || '2', 10);
            if (!rows || !cols || rows < 2 || cols < 1) return;
            var html = '<table style="width:100%;border-collapse:collapse;table-layout:fixed;margin:1.5rem 0;"><thead><tr>';
            for (var c=0; c<cols; c++) html += '<th style="background:#0f1e6c;color:#fff !important;padding:10px 14px;text-align:left;border:1px solid #1e3a8a;word-break:break-word;vertical-align:middle;">Header '+(c+1)+'</th>';
            html += '</tr></thead><tbody>';
            for (var r=1; r<rows; r++) {
              html += '<tr>';
              for (var cc=0; cc<cols; cc++) html += '<td style="padding:10px 14px;border-bottom:1px solid #e2e8f0;text-align:left;border-left:1px solid #e2e8f0;border-right:1px solid #e2e8f0;word-break:break-word;vertical-align:middle;">Cell</td>';
              html += '</tr>';
            }
            html += '</tbody></table><p><br></p>';
            var range = q.getSelection(true);
            q.clipboard.dangerouslyPasteHTML(range ? range.index : q.getLength(), html);
          });
          toolbar.appendChild(tableBtn);
        }
      }
    } catch (err) { ta.style.display = ''; holder.remove(); }
  });
  function fixQuillTables(html) {
    var temp = document.createElement('div');
    temp.innerHTML = html;
    temp.querySelectorAll('table').forEach(function(table) {
      if (table.querySelector('thead')) return;
      var firstTr = table.querySelector('tr');
      if (!firstTr) return;
      var isRtl = document.documentElement.dir === 'rtl' || document.documentElement.lang === 'ar';
      var thAlign = isRtl ? 'right' : 'left';
      var ths = [];
      var tds = firstTr.querySelectorAll('td');
      // Broken single-cell header: split Header1Header2 / نوع المنشأةمتطلبات
      if (tds.length === 1) {
        var maxColsTmp = 0;
        table.querySelectorAll('tr').forEach(function(tr){ maxColsTmp = Math.max(maxColsTmp, tr.querySelectorAll('td,th').length); });
        if (maxColsTmp > 1) {
          var rawSingle = (tds[0].innerText || tds[0].textContent || '').trim();
          var partsSingle = splitSingleHeaderText(rawSingle, maxColsTmp);
          if (partsSingle) {
            partsSingle.forEach(function(txt){
              var th = document.createElement('th');
              th.textContent = txt;
              th.setAttribute('style', 'background:#0f1e6c;color:#fff !important;padding:10px 14px;text-align:' + thAlign + ';border:1px solid #1e3a8a;word-break:break-word;vertical-align:middle;');
              ths.push(th);
            });
          }
        }
      }
      if (ths.length === 0 && tds.length > 0) {
        tds.forEach(function(td) {
          var th = document.createElement('th');
          th.innerHTML = td.innerHTML;
          th.setAttribute('style', 'background:#0f1e6c;color:#fff !important;padding:10px 14px;text-align:' + thAlign + ';border:1px solid #1e3a8a;word-break:break-word;vertical-align:middle;');
          ths.push(th);
        });
      } else {
        firstTr.querySelectorAll('th').forEach(function(th) {
          th.setAttribute('style', 'background:#0f1e6c;color:#fff !important;padding:10px 14px;text-align:' + thAlign + ';border:1px solid #1e3a8a;word-break:break-word;vertical-align:middle;');
          ths.push(th);
        });
        if (ths.length === 0) return;
      }
      var thead = document.createElement('thead');
      var tr = document.createElement('tr');
      ths.forEach(function(th){ tr.appendChild(th); });
      thead.appendChild(tr);
      var tbody = document.createElement('tbody');
      var allTrs = table.querySelectorAll('tr');
      for (var i=1; i<allTrs.length; i++) {
        var r = allTrs[i];
        r.querySelectorAll('td').forEach(function(td){
          if (!td.getAttribute('style')) {
            td.setAttribute('style', 'padding:10px 14px;border-bottom:1px solid #e2e8f0;text-align:' + thAlign + ';border-left:1px solid #e2e8f0;border-right:1px solid #e2e8f0;word-break:break-word;vertical-align:middle;');
          }
        });
        tbody.appendChild(r.cloneNode(true));
      }
      // Handle tables that had no rows beyond header
      if (tbody.children.length === 0 && ths.length > 0) {
        // keep empty tbody
      }
      table.innerHTML = '';
      if (isRtl) { table.setAttribute('dir','rtl'); table.style.direction='rtl'; }
      table.style.width='100%';
      table.style.borderCollapse='collapse';
      table.style.tableLayout='fixed';
      table.style.margin='1.5rem 0';
      table.appendChild(thead);
      table.appendChild(tbody);
    });
    return temp.innerHTML;
  }
  document.addEventListener('submit', function (e) {
    if (quills.length === 0) return;
    var form = e.target.closest('form[data-admin-form]');
    if (!form) return;
    quills.forEach(function (rec) {
      if (form.contains(rec.ta)) {
        var html = rec.q.root.innerHTML;
        html = fixQuillTables(html);
        rec.ta.value = html;
      }
    });
  });

  // Product form: filter category options by selected brand
  var brandSelect = document.querySelector('select[data-brand-filter]');
  var categorySelect = document.querySelector('select[data-brand-category]');
  if (brandSelect && categorySelect) {
    var options = Array.prototype.slice.call(categorySelect.options);
    function applyBrandFilter() {
      var brand = brandSelect.value;
      var anyVisible = false;
      options.forEach(function (opt) {
        if (opt.value === '') { opt.hidden = false; opt.disabled = false; return; }
        var matches = !brand || opt.getAttribute('data-brand') === brand;
        opt.hidden = !matches;
        opt.disabled = !matches;
        if (matches && opt.value !== '') anyVisible = true;
      });
      if (!brand || categorySelect.value && categorySelect.options[categorySelect.selectedIndex] && categorySelect.options[categorySelect.selectedIndex].hidden) {
        categorySelect.value = '';
      }
      var placeholder = categorySelect.options[0];
      if (placeholder) {
        placeholder.textContent = brand ? (categorySelect.getAttribute('data-placeholder-brand') || 'Select a category for this brand…') : (categorySelect.getAttribute('data-placeholder-all') || 'Select a brand first to narrow down categories…');
      }
    }
    brandSelect.addEventListener('change', applyBrandFilter);
    applyBrandFilter();
  }
})();
