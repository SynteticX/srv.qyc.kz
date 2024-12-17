function popup(logLvl, txt) {
    let alert = document.createElement('div');
        alert.style.position = 'fixed';
        alert.style.right = '-250px';
        alert.style.top = '100px';
        alert.style.zIndex = '9999';
        alert.style.transition = 'right 2s';
        alert.classList.add('alert');
        if (logLvl == 'success') {
            alert.classList.add('alert-success');
        }
        if (logLvl == 'danger' || logLvl == 'error' ) {
            alert.classList.add('alert-danger');
        }
        if (logLvl == 'warning') {
            alert.classList.add('alert-warning');
        }
        alert.setAttribute('role', 'alert');
        alert.textContent = txt;
        document.body.appendChild(alert);
        let activeAlert = () => {
            alert.style.right = '25px';
        }
        let hideAlert = () => {
            alert.style.right = '-250px';
        }
        setTimeout(activeAlert, 200);
        setTimeout(hideAlert, 5000);

        alert.addEventListener("click", (e) => {
            e.target.remove();
        })
}