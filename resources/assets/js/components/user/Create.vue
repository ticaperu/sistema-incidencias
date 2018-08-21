<template>
    <div class="card">
        <div class="card-header">
            Crear User
        </div>
        <form action="" v-on:submit.prevent="newUser()">
            <user-form :user="user"  :errors="errors"></user-form>

            <div class="card-footer">
                <button class="btn btn-primary" type="submit" :disabled="loading">
                    Guardar
                </button>
                <a class="btn btn-danger" :href="cancel_url">
                    Cancelar
                </a>
            </div>
        </form>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                user: {
                    state:'Activo',
                    rol_id:''
                },
                errors: [],
                loading: false,
                polygon:{},
                cancel_url:route('user.index')
            }
        },
        methods: {
            newUser() {
                this.loading = true;
                axios.post(route('user.store'), this.user).then((response) => {
                    if (response.data.success) {
                       window.location.href = this.cancel_url;
                    }

                    this.loading = false;

                }).catch((error) => {
                    if (error.response.status == 422) {
                        this.errors = error.response.data.errors;
                    }

                    this.loading = false;

                })
            }
        }
    }
</script>