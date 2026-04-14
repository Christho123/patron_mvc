/* Indicador de fortaleza de contraseña en tiempo real */
        const pwInput = document.getElementById('password');
        const bars = document.querySelectorAll('#strengthBars .bar');
        const colors = ['#f43f5e','#fb923c','#facc15','#10b981'];

        pwInput.addEventListener('input', function() {
            const val = this.value;
            let score = 0;
            if (val.length >= 4) score++;
            if (val.length >= 8) score++;
            if (/[A-Z]/.test(val) && /[a-z]/.test(val)) score++;
            if (/\d/.test(val) && /[^A-Za-z0-9]/.test(val)) score++;
            bars.forEach((bar, i) => {
                bar.style.background = i < score
                    ? colors[Math.min(score - 1, 3)]
                    : 'rgba(100,116,139,0.2)';
            });
        });
        function showToast(msg){
    Swal.fire({
        toast: true,
        position: 'top-end',
        icon: 'success',
        title: msg,
        showConfirmButton: false,
        timer: 3000
    });
}