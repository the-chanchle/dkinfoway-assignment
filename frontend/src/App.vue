<template>
    <div id="app">
        <nav v-if="isAuthenticated">
            <router-link to="/">Stock List</router-link> |
            <router-link to="/bulk">Bulk Stock Entry</router-link> |
            <button @click="logoutUser">Logout</button>
        </nav>
        <router-view />
    </div>
</template>


<script>
import { mapGetters, mapActions } from 'vuex';


export default {
computed: {
...mapGetters(['isAuthenticated']),
},
methods: {
...mapActions(['logout']),
logoutUser() {
this.logout();
this.$router.push('/login');
},
},
};
</script>


<style>
#app {
font-family: Avenir, Helvetica, Arial, sans-serif;
margin: 20px;
}
nav {
margin-bottom: 20px;
}
nav a {
margin-right: 10px;
text-decoration: none;
color: #42b983;
}
nav a.router-link-exact-active {
font-weight: bold;
}
button {
cursor: pointer;
}
</style>