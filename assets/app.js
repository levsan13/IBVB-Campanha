// Front-end logic: busca total, valida formulário, gera payload Pix, mostra QR usando Google Chart API
(function(){
  const formatBR = n=>n.toLocaleString('pt-BR',{minimumFractionDigits:2, maximumFractionDigits:2});

  async function fetchTotal(){
    try{
      const res = await fetch('/server/total.php');
      if(!res.ok) throw 0;
      const data = await res.json();
      const total = Number(data.total) || 0;
      document.querySelectorAll('#total-display, #total-display-2').forEach(el=>el.textContent = 'R$ '+formatBR(total));
      // if you want a goal, change goal value
      const goal = Number(data.goal) || 10000;
      const pct = Math.min(100, Math.round((total/goal)*100));
      const pbar = document.getElementById('progress-bar'); if(pbar) pbar.style.width = pct+'%';
    }catch(e){ console.warn('Não conseguiu buscar total',e) }
  }

  // On load
  document.addEventListener('DOMContentLoaded', ()=>{
    fetchTotal();
    // donation page logic
    const proceed = document.getElementById('proceed');
    if(proceed){
      proceed.addEventListener('click', onProceed);
    }
    const confirmBtn = document.getElementById('confirm-donation');
    if(confirmBtn) confirmBtn.addEventListener('click', onConfirm);
  });

  function parseAmountRaw(v){
    // accepts formats like 50,00 or 50.00 or 50
    if(!v) return null;
    v = String(v).trim().replace('\u00A0','');
    v = v.replace(/\./g,'').replace(',','.');
    const num = parseFloat(v);
    return isNaN(num)?null:num;
  }

  function onProceed(){
    const amountI = document.getElementById('amount');
    const phoneI = document.getElementById('phone');
    const cityI = document.getElementById('city');
    const amount = parseAmountRaw(amountI.value);
    if(!amount || amount<=0){ alert('Informe um valor válido.'); return; }
    if(!phoneI.value.trim()){ alert('Informe o telefone.'); return; }
    if(!cityI.value.trim()){ alert('Informe a cidade.'); return; }

    // build PIX payload (simple copy-paste string + basic EMV-like payload for QR)
    const pixKey = 'valedebencaosc@gmail.com';
    const name = 'Yuri Moura Queiroz';
    const city = 'Fortaleza';
    // Create a simple copy string for user
    const copy = `PIX ${pixKey} valor R$ ${amount.toFixed(2).replace('.',',')} — ${name} — Fortaleza - CE`;
    document.getElementById('pix-payload').textContent = copy;
    document.getElementById('pix-key').textContent = pixKey;
    // generate QR using Google Chart API for convenience
    const qrData = encodeURIComponent(copy);
    const qrUrl = `https://chart.googleapis.com/chart?chs=300x300&cht=qr&chl=${qrData}`;
    document.getElementById('pix-qr').src = qrUrl;

    document.getElementById('pix-area').classList.remove('hidden');
    // save data temporarily in dataset for confirm use
    const form = document.getElementById('donation-form');
    form.dataset.amount = amount.toFixed(2);
    form.dataset.phone = phoneI.value.trim();
    form.dataset.city = cityI.value.trim();
    window.scrollTo({top:0, behavior:'smooth'});
  }

  async function onConfirm(){
    const form = document.getElementById('donation-form');
    const amount = form.dataset.amount; const phone = form.dataset.phone; const city = form.dataset.city;
    if(!amount){ alert('Nenhum valor para confirmar.'); return; }
    const btn = document.getElementById('confirm-donation'); btn.disabled = true; btn.textContent = 'Gravando...';
    try{
      const res = await fetch('server/process_donation.php',{
        method:'POST', headers:{'Content-Type':'application/json'},
        body: JSON.stringify({amount,phone,city})
      });
      const data = await res.json();
      const msgEl = document.getElementById('msg');
      if(data.success){ msgEl.textContent = 'Doação registrada. Obrigado!';
        // atualizar total
        await fetchTotal();
      } else {
        msgEl.textContent = 'Erro ao registrar: '+(data.error||'');
      }
    }catch(e){ console.error(e); alert('Erro ao conectar com o servidor.'); }
    btn.disabled = false; btn.textContent = 'Confirmar doação (gravar)';
  }

})();