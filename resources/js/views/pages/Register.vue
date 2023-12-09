<template>
    <div class="card bg-white">
        <div class="card-body p-5">
            <form class="mb-3 mt-md-4" @submit.prevent="register" id="form">
                <h2 class="fw-bold mb-5 text-center">
                    Let's get started!
                </h2>
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input required type="email" class="form-control" :class="{'is-invalid': errors.email}" id="email" name="email" placeholder="name@example.com" v-model="inputs.email" />
                    <div class="invalid-feedback">
                        {{ errors.email }}
                    </div>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input required type="password" class="form-control" id="password" name="password" placeholder="*******" v-model="inputs.password" />
                </div>
                <div class="d-grid">
                    <button class="btn btn-outline-primary fw-bold" type="submit">
                        Register
                    </button>
                </div>
            </form>
            <div>
                <p class="mb-0 text-center">
                    Already have an account?
                    <router-link to="/login" class="text-primary">Login</router-link>
                    here!
                </p>
            </div>
        </div>
    </div>
</template>

<script>
import { useAuthStore } from '@/stores/authStore';

export default {
    name: "register",
    data () {
        return {
            inputs: {
                email: null,
                password: null,
            },
            errors: {
                email: '',
                password: '',
            },
        }
    },
    methods: {
        register() {
            var form = document.getElementById('form');
            var formData = new FormData(form);

            form.reset();
            this.errors = {};
            
            useAuthStore()
                .register(formData)
                .catch((error) => {
                    const errors = Object.entries(error.response.data.errors);
                    errors.map(([key, val] = error) => {
                        this.errors[key] = val[0];
                    });
                });
        },
    },
};
</script>
