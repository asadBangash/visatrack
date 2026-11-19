<?php

$source = dirname(__DIR__, 2) . '/../usstate/visa-status.html';
$target = dirname(__DIR__, 2) . '/resources/views/public/visa-status.blade.php';
$html = file_get_contents($source);

$html = str_replace('href="index.html"', 'href="{{ route(\'home\') }}"', $html);

$html = preg_replace(
    '#<select id="location" required="">.*?</select>#s',
    <<<'BLADE'
<select id="location" name="location_id" required="">
<option value="">- SELECT ONE -</option>
@foreach($locations as $location)
<option value="{{ $location->id }}">{{ $location->display_name }}</option>
@endforeach
</select>
BLADE,
    $html,
    1
);

$html = str_replace(
    '<form autocomplete="off" id="form">',
    '<form autocomplete="off" id="form" method="post" action="{{ route(\'visa-status.check\') }}">@csrf',
    $html
);

$html = str_replace(
    '<div class="samples"><button data-sample="DEMO-1001"',
    '<div class="samples"><button data-sample="DEMO-AA001001"',
    $html
);
$html = str_replace('data-sample="DEMO-1002"', 'data-sample="DEMO-AA001002"', $html);
$html = str_replace('data-sample="DEMO-1003"', 'data-sample="DEMO-AA001003"', $html);

$html = str_replace(
    '<span id="challenge">XCCV7</span>',
    '<span id="challenge">{{ $captchaCode }}</span>',
    $html
);

$newScript = <<<'JS'
<script>(()=>{
const refreshUrl=@json(route('visa-status.captcha'));
const checkUrl=@json(route('visa-status.check'));
const csrfToken=document.querySelector('meta[name="csrf-token"]')?.content||document.querySelector('input[name="_token"]')?.value;
const defaultType=@json($defaultType);
const samples=@json([
  {id:'DEMO-AA001001',passport:'SAMPLE001',surname:'SINGH'},
  {id:'DEMO-AA001002',passport:'SAMPLE002',surname:'AHMED'},
  {id:'DEMO-AA001003',passport:'SAMPLE003',surname:'MALIK'},
]);
const $=id=>document.getElementById(id);
const modal=$('modal-overlay'),dialog=document.querySelector('.status-window');let priorFocus=null;
function error(msg){$('result').hidden=false;$('result').textContent=msg;}
function clearError(){$('result').hidden=true;$('result').textContent='';}
function showPopup(data){priorFocus=document.activeElement;$('popup-status').textContent=data.status;$('popup-app').textContent=data.confirmation_number;$('popup-created').textContent=data.created;$('popup-updated').textContent=data.updated;$('popup-message').textContent=data.message;$('popup-location').textContent='Selected location: '+data.location+' (demonstration only)';document.querySelector('.popup-heading strong').textContent=$('type').value==='IV'?'IMMIGRANT VISA APPLICATION':'NONIMMIGRANT VISA APPLICATION';modal.hidden=false;dialog.focus();}
function close(){modal.hidden=true;if(priorFocus&&priorFocus.focus)priorFocus.focus();}
$('close-result').addEventListener('click',close);
modal.addEventListener('click',e=>{if(e.target===modal)close();});
document.addEventListener('keydown',e=>{if(e.key==='Escape'&&!modal.hidden)close();});
async function refreshCaptcha(){const res=await fetch(refreshUrl,{headers:{'Accept':'application/json'}});if(!res.ok)return;const data=await res.json();$('challenge').textContent=data.code;$('code').value='';clearError();}
$('refresh').addEventListener('click',()=>{refreshCaptcha();});
$('audio').addEventListener('click',()=>{const code=$('challenge').textContent;if('speechSynthesis' in window){const u=new SpeechSynthesisUtterance(code.split('').join(' '));u.rate=.65;speechSynthesis.speak(u);}else alert('Demo verification code: '+code);});
for(const button of document.querySelectorAll('[data-sample]'))button.addEventListener('click',()=>{const sample=samples.find(s=>s.id===button.dataset.sample);if(!sample)return;$('application').value=sample.id;$('passport').value=sample.passport;$('surname').value=sample.surname;$('code').value=$('challenge').textContent;if(!$('location').value){const opts=[...$('location').options];const pak=opts.find(o=>o.textContent.includes('PAKISTAN, ISLAMABAD'));if(pak)$('location').value=pak.value;}clearError();});
$('form').addEventListener('submit',async e=>{e.preventDefault();clearError();if(!$('location').value){error('Please select a demonstration location.');return;}const payload={visa_type:$('type').value,location_id:$('location').value,confirmation_number:$('application').value.trim(),passport_number:$('passport').value.trim(),surname:$('surname').value.trim(),captcha:$('code').value.trim(),_token:csrfToken};
const res=await fetch(checkUrl,{method:'POST',headers:{'Accept':'application/json','Content-Type':'application/json','X-CSRF-TOKEN':csrfToken,'X-Requested-With':'XMLHttpRequest'},body:JSON.stringify(payload)});
const data=await res.json().catch(()=>({}));if(!res.ok){error(data.message||'The information you entered does not match our records.');if(res.status===422){await refreshCaptcha();}return;}showPopup(data.popup);});
if(defaultType==='IV')$('type').value='IV';
})();
</script>
JS;

$html = preg_replace('#<script>\(\(\)=>\{.*?</script>#s', $newScript, $html, 1);

if (! str_contains($html, 'csrf-token')) {
    $html = str_replace(
        '<meta content="width=device-width,initial-scale=1" name="viewport"/>',
        '<meta content="width=device-width,initial-scale=1" name="viewport"/><meta name="csrf-token" content="{{ csrf_token() }}"/>',
        $html
    );
}

file_put_contents($target, $html);
echo "Built visa-status blade\n";