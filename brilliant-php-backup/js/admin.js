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
        modules: { toolbar: [[{ header: [1,2,3,false] }], ['bold','italic','underline','strike'], [{ list:'ordered'},{list:'bullet'}], ['link','image'], ['clean']] }
      });
      q.root.innerHTML = ta.value || '';
      quills.push({ ta: ta, q: q });
    } catch (err) { ta.style.display = ''; holder.remove(); }
  });
  document.addEventListener('submit', function (e) {
    if (quills.length === 0) return;
    var form = e.target.closest('form[data-admin-form]');
    if (!form) return;
    quills.forEach(function (rec) { if (form.contains(rec.ta)) rec.ta.value = rec.q.root.innerHTML; });
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
