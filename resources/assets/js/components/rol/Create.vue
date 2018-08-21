<template>
    <div class="card">
        <div class="card-header">
            Crear Rol
        </div>
        <form action="" v-on:submit.prevent="newRol()">
            <rol-form :rol="rol"  :errors="errors"></rol-form>

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
                rol: {
                    descripcion: '',
                    estado:'Activo',
                    permisos:[]
                },
                errors: [],
                loading: false,
                polygon:{},
                cancel_url:route('rol.index')
            }
        },
        methods: {
            newRol() {
                this.loading = true;
                axios.post(route('rol.store'), this.rol).then((response) => {
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