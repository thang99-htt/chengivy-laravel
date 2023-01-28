<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-4 py-3 bg-dark border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray active:bg-dark focus:outline-none focus:border-dark-900 focus:ring ring-dark-300 disabled:opacity-25 transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>
