document.addEventListener('DOMContentLoaded', () => {
  const search = document.getElementById('tableSearch');
  const table = document.getElementById('filterTable');
  if (search && table) {
    search.addEventListener('input', () => {
      const q = search.value.toLowerCase().trim();
      table.querySelectorAll('tbody tr').forEach(row => {
        row.hidden = !row.innerText.toLowerCase().includes(q);
      });
    });
  }
  document.querySelectorAll('form').forEach(form => {
    form.addEventListener('submit', () => {
      const submit = form.querySelector('button[type="submit"]');
      if (submit && !submit.disabled) {
        submit.dataset.originalText = submit.textContent;
        submit.textContent = 'Please wait…';
      }
    });
  });
});
