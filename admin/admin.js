// Simple delegations for filling edit modals
document.addEventListener('click', (e)=>{
  const t = e.target.closest('[data-edit]');
  if(!t) return;
  const target = document.querySelector(t.getAttribute('data-target'));
  if(!target) return;
  target.querySelectorAll('[data-field]').forEach(inp=>{
    const name = inp.getAttribute('data-field');
    const val = t.getAttribute('data-'+name) ?? '';
    if (inp.tagName==='TEXTAREA') inp.value = val;
    else inp.value = val;
  });
});
