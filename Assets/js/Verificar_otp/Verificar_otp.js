document.addEventListener('DOMContentLoaded', () => {
    const inputs = document.querySelectorAll('.otp-input');
    const hiddenInput = document.getElementById('otpHidden');

    if (!inputs.length || !hiddenInput) {
        return;
    }

    inputs[0].focus();

    inputs.forEach((input, index) => {
        input.addEventListener('input', (e) => {
            e.target.value = e.target.value.replace(/[^0-9]/g, '').slice(0, 1);
            if (e.target.value && index < inputs.length - 1) {
                inputs[index + 1].focus();
            }
        });

        input.addEventListener('keydown', (e) => {
            if (e.key === 'Backspace' && !e.target.value && index > 0) {
                inputs[index - 1].focus();
            }
        });

        input.addEventListener('paste', (e) => {
            e.preventDefault();
            const text = (e.clipboardData || window.clipboardData).getData('text');
            const digits = text.replace(/[^0-9]/g, '').slice(0, inputs.length).split('');

            inputs.forEach((inp, i) => {
                inp.value = digits[i] || '';
            });

            const focusIndex = Math.min(digits.length, inputs.length - 1);
            inputs[focusIndex].focus();
        });
    });

    window.submitOTP = function() {
        let otpValue = '';
        let isValid = true;

        inputs.forEach((input) => {
            otpValue += input.value;
            if (input.value === '') isValid = false;
        });

        if (!isValid || otpValue.length !== 6) {
            Swal.fire({
                icon: 'warning',
                title: 'Codigo incompleto',
                text: 'Por favor ingresa los 6 digitos.',
                confirmButtonColor: '#10b981'
            });
            return false;
        }

        hiddenInput.value = otpValue;
        return true;
    };
});
    