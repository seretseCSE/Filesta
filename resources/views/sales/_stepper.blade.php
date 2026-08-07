<div class="flex items-center justify-between" data-stepper>
    <button
        type="button"
        data-step="-1"
        class="flex h-14 w-14 items-center justify-center rounded-full border border-gray-300 bg-white text-3xl leading-none text-gray-700 active:bg-gray-100"
        aria-label="Decrease quantity"
    >&minus;</button>
    <input
        type="number"
        name="quantity"
        value="{{ old('quantity', $quantity ?? 1) }}"
        min="1"
        inputmode="numeric"
        data-stepper-input
        required
        class="w-24 rounded-xl border border-gray-300 px-4 py-3 text-center focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200"
    >
    <button
        type="button"
        data-step="1"
        class="flex h-14 w-14 items-center justify-center rounded-full border border-gray-300 bg-white text-3xl leading-none text-gray-700 active:bg-gray-100"
        aria-label="Increase quantity"
    >+</button>
</div>

<script>
    document.querySelectorAll('[data-stepper]').forEach(function (stepper) {
        var input = stepper.querySelector('[data-stepper-input]');
        var min = parseInt(input.getAttribute('min') || '1', 10);
        stepper.querySelectorAll('[data-step]').forEach(function (btn) {
            btn.addEventListener('click', function () {
                var current = parseInt(input.value, 10);
                if (isNaN(current)) current = min;
                input.value = Math.max(min, current + parseInt(btn.dataset.step, 10));
            });
        });
    });
</script>
