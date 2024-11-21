<template>
    <div class="header">
        <h2>Budget-Manager</h2>
        <a href="#" @click.prevent="logout" class="logout-link">Ausloggen</a>
    </div>
</template>

<script setup>
const emit = defineEmits(['logout']);

const logout = () => {
    emit('logout');
};
</script>

<style scoped>
.header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px;
    border-bottom: 1px solid #ccc;
}

h2 {
    margin: 0;
    font-size: 24px;
}

.logout-link {
    font-size: 16px;
    color: rgb(25, 90, 130);
    text-decoration: none;
    padding: 5px 10px;
    border: 1px solid rgb(25, 90, 130);
    border-radius: 4px;
    transition: background-color 0.3s, color 0.3s;
}

.logout-link:hover {
    background-color: rgb(25, 90, 130);
    color: #fff;
}
</style>