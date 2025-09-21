@push('scripts')
<script>
// Toggle user mode visibility if radios exist
(function(){
  function toggleUserMode(){
    const mode = document.querySelector('input[name="user_mode"]:checked')?.value || 'existing';
    const ex = document.getElementById('existingUserWrap');
    const nw = document.getElementById('newUserWrap');
    if(ex) ex.style.display = mode==='existing' ? '' : 'none';
    if(nw) nw.style.display = mode==='new' ? '' : 'none';
  }
  document.addEventListener('DOMContentLoaded', ()=>{
    document.querySelectorAll('input[name="user_mode"]').forEach(r=>r.addEventListener('change', toggleUserMode));
    toggleUserMode();
  });
})();

// Province/City fetch for admin companies form
(function(){
  document.addEventListener('DOMContentLoaded', async () => {
    const pSel = document.getElementById('admProvinceSelect');
    const cSel = document.getElementById('admCitySelect');
    const pName = document.getElementById('admProvinceName');
    const cName = document.getElementById('admCityName');
    if(!pSel || !cSel) return;
    async function loadProv(){
      pSel.innerHTML = '<option value="">Memuat...</option>';
      try{
        const res = await fetch("{{ url('api/wilayah/provinces') }}");
        const json = await res.json();
        const list = Array.isArray(json.data) ? json.data : [];
        pSel.innerHTML = '<option value="">Pilih</option>' + list.map(p=>`<option value="${p.code}">${p.name}</option>`).join('');
        const currentCode = pSel.dataset.currentCode || '';
        if(currentCode){ pSel.value = currentCode; }
        pName.value = pSel.options[pSel.selectedIndex]?.text || '';
        if(pSel.value){ await loadCities(pSel.value); }
      }catch(e){ pSel.innerHTML='<option value="">Gagal memuat</option>'; }
    }
    async function loadCities(code){
      if(!code){ cSel.innerHTML='<option value="">Pilih provinsi dulu</option>'; cSel.disabled=true; return; }
      cSel.disabled = true; cSel.innerHTML='<option value="">Memuat...</option>';
      try{
        const res = await fetch(`{{ url('api/wilayah/regencies') }}/${code}`);
        const json = await res.json();
        const list = Array.isArray(json.data) ? json.data : [];
        cSel.innerHTML = '<option value="">Pilih</option>' + list.map(c=>`<option value="${c.code}">${c.name}</option>`).join('');
        const currentCode = cSel.dataset.currentCode || '';
        if(currentCode){ cSel.value = currentCode; }
        cSel.disabled = false;
        cName.value = cSel.options[cSel.selectedIndex]?.text || '';
      }catch(e){ cSel.innerHTML='<option value="">Gagal memuat</option>'; }
    }
    pSel.addEventListener('change', async (e)=>{
      const code = e.target.value; pName.value = pSel.options[pSel.selectedIndex]?.text || '';
      await loadCities(code);
    });
    cSel.addEventListener('change', ()=>{ cName.value = cSel.options[cSel.selectedIndex]?.text || ''; });
    await loadProv();
  });
})();
</script>
@endpush
