function lmAlert(text, okBtnTxt, noBtnTxt, okDataSet){
	let container = document.createElement('div');
	let overlay = document.createElement('div');
	let alertBox = document.createElement('div');
	let alertClose = document.createElement('span');
	let alertContent = document.createElement('div');
	let mainTxt = document.createTextNode(text);
	let alertFoot = document.createElement('div');
	let okBtn = document.createElement('button');
	let noBtn = document.createElement('button');
	let okTxt = document.createTextNode(okBtnTxt);
	let noTxt = document.createTextNode(noBtnTxt);
  
	container.classList.add('smr-custome-alert');
	overlay.classList.add('smr-alert-overlay');
	alertBox.classList.add('smr-alert-box');
	alertContent.classList.add('smr-alert-content');
	alertFoot.classList.add('smr-alert-footer');
	okBtn.classList.add('smr-alert-ok-btn');
	noBtn.classList.add('smr-alert-no-btn');
  
	container.appendChild(overlay);
	container.appendChild(alertBox);
  
	alertBox.appendChild(alertContent);
	alertContent.innerHTML = text;
	alertFoot.appendChild(okBtn);
	okBtn.appendChild(okTxt);
	alertFoot.appendChild(noBtn);
	noBtn.appendChild(noTxt);
	alertBox.appendChild(alertFoot);
	alertBox.appendChild(alertClose);
  
	document.body.appendChild(container);
	alertClose.innerText = '×';
  
	noBtn.addEventListener('click', () => {
		overlay.style.opacity = '0';
		alertBox.style.top = '-100px';
		alertBox.style.opacity = '0';
  
		setTimeout(() => {
			container.remove();
		}, 500);
	});
  
  //   okBtn.addEventListener('click', () => {
  //     overlay.style.opacity = '0';
  //     alertBox.style.top = '-100px';
  //     alertBox.style.opacity = '0';
  
  //     setTimeout(() => {
  //         container.remove();
  //     }, 500);
  // });
  
	alertClose.addEventListener('click', () => {
		overlay.style.opacity = '0';
		alertBox.style.top = '-100px';
		alertBox.style.opacity = '0';
  
		setTimeout(() => {
			container.remove();
		}, 500);
	});
  
	setTimeout(() => {
		alertBox.style.opacity = '1';
		alertBox.style.top = '100px';
	}, 100);
  
  }
  /*End alert*/
  
  /*Start alert info box*/
  function lmAlertInfo(text){
	let container = document.createElement('div');
	let alertBox = document.createElement('div');
	let alertClose = document.createElement('span');
	let pElem = document.createElement('p');
	let mainTxt = document.createTextNode(text);
  
	container.classList.add('smr-custome-alert-info');
	alertBox.classList.add('smr-alert-info');
  
	container.appendChild(alertBox);
  
	alertBox.appendChild(pElem);
	pElem.appendChild(mainTxt);
	alertBox.appendChild(alertClose);
  
	document.body.appendChild(container);
	alertClose.innerText = '×';
  
  
	alertClose.addEventListener('click', () => {
	  container.style.right  ='-15%';
	  container.style.opacity  ='0';
  
		setTimeout(() => {
			container.remove();
		}, 500);
	});
  
  setTimeout(() => {
	  container.style.right  ='1%';
	  container.style.opacity  ='1';
  }, 100);
  
	setTimeout(() => {
	  container.style.right  ='-15%';
	  container.style.opacity  ='0';
  
	  setTimeout(() => {
		  container.remove();
	  }, 1000);
  
  }, 10000);
  
  }
  /*Start alert info box*/
setTimeout(() => {
	if(new URL(location.href).searchParams.get("account") === 'notcomplete'){
		lmAlert(`<h2>استكمل بياناتاك</h2><p>يجب إستكمال بيانات ملفك الشخصي وكتابة رقم الهوية.</p>`, 'استكمال البيانات', 'إلغاء')
		//location.href = '/dashboard/settings'
		document.querySelector('.smr-alert-ok-btn').addEventListener('click', () => {
			location.href = '/dashboard/settings'
		})
	}
}, 1500);

let lmSert = document.querySelector('#tutor-pro-certificate-preview');
if(lmSert){
	let serUrl = lmSert.dataset.src
	if(serUrl){
		lmSert.src = serUrl
		console.log('certificate displaied')
	}else{
		console.log('certificate not displaied')
	}
	console.log('found certificate')
	//console.log(lmSert)
}else{
	console.log('not found certificate')
}

