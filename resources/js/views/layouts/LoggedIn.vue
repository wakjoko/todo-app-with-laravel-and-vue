<template>
    <header class="navbar sticky-top bg-dark flex-md-nowrap p-0 shadow" data-bs-theme="dark">
        <a class="navbar-brand col-md-3 col-lg-2 me-0 px-3 fs-6 text-white" href="#">Appetiser</a>

        <ul class="navbar-nav flex-row d-md-none">
            <li class="nav-item text-nowrap">
                <button class="nav-link px-3 text-white" type="button" data-bs-toggle="offcanvas"
                    data-bs-target="#sidebarMenu" aria-controls="sidebarMenu" aria-expanded="false"
                    aria-label="Toggle navigation">
                    <i class="bi bi-list"></i>
                </button>
            </li>
        </ul>
    </header>

    <div class="container-fluid">
        <div class="row">
            <div class="sidebar border border-right col-md-3 col-lg-2 p-0 bg-body-tertiary">
                <div class="offcanvas-md offcanvas-end bg-body-tertiary" tabindex="-1" id="sidebarMenu" aria-labelledby="sidebarMenuLabel">
                    <div class="offcanvas-header">
                        <h5 class="offcanvas-title" id="sidebarMenuLabel">
                            Tasks
                        </h5>
                        <button id="close-menu" type="button" class="btn-close" data-bs-dismiss="offcanvas" data-bs-target="#sidebarMenu" aria-label="Close"></button>
                    </div>
                    <div class="offcanvas-body d-md-flex flex-column p-0 pt-lg-3 overflow-y-auto">
                        <ul class="nav flex-column">
                            <li class="nav-item">
                                <router-link to="/tasks/todo" class="nav-link d-flex align-items-center gap-2" :class="{'text-muted': $router.currentRoute.value.name != 'todo'}">
                                    <i class="bi bi-clipboard-pulse"></i>
                                    Todo
                                </router-link>
                            </li>
                            <li class="nav-item">
                                <router-link to="/tasks/completed" class="nav-link d-flex align-items-center gap-2" :class="{'text-muted': $router.currentRoute.value.name != 'completed'}">
                                    <i class="bi bi-clipboard-check"></i>
                                    Completed
                                </router-link>
                            </li>
                            <li class="nav-item">
                                <router-link to="/tasks/archived" class="nav-link d-flex align-items-center gap-2" :class="{'text-muted': $router.currentRoute.value.name != 'archived'}">
                                    <i class="bi bi-clipboard-x"></i>
                                    Archived
                                </router-link>
                            </li>
                        </ul>

                        <hr class="my-3" />

                        <ul class="nav flex-column mb-auto">
                            <li class="nav-item">
                                <button class="nav-link d-flex align-items-center gap-2 text-muted"
                                    @click="logout">
                                    <i class="bi bi-door-closed"></i>
                                    Logout
                                </button>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 vh-100">
                <router-view />
            </main>
        </div>
    </div>
</template>

<script setup>
import { useRoute } from 'vue-router';
import { watch } from 'vue';
import { useAuthStore } from '@/stores/authStore';

const route = useRoute();

watch(
    () => route.name,
    () => document.querySelector('#close-menu').click(),
)

function logout() {
    useAuthStore().logout(true);
}
</script>

<style>
.bi {
  display: inline-block;
  width: 1rem;
  height: 1rem;
}

/*
 * Sidebar
 */

@media (min-width: 768px) {
  .sidebar .offcanvas-lg {
    position: -webkit-sticky;
    position: sticky;
    top: 48px;
  }
}

.sidebar .nav-link {
  font-size: .875rem;
  font-weight: 500;
}

.sidebar .nav-link.router-link-active {
  color: #2470dc;
}

.sidebar-heading {
  font-size: .75rem;
}

/*
 * Navbar
 */

.navbar-brand {
  padding-top: .75rem;
  padding-bottom: .75rem;
  background-color: rgba(0, 0, 0, .25);
  box-shadow: inset -1px 0 0 rgba(0, 0, 0, .25);
}

.navbar .form-control {
  padding: .75rem 1rem;
}
</style>