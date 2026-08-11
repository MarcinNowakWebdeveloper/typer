<template>
    <main class="container py-4">
        <div class="regulations">
            <iframe src="/api/regulations"></iframe>
        </div>
    </main>
</template>

<style scoped>
.regulations {
    height: calc(100vh - 180px);
}

.regulations iframe {
    width: 100%;
    height: 100%;
    border: 0;
}
</style>
