@props(['for'])

<template x-if="{{ $for }}">
    <p {{ $attributes->merge(['class' => 'mt-1 text-xxs text-red-600']) }} x-text="{{ $for }}[0]"></p>
</template>
