<template>
    <div class="card">
        <div class="card-header">
            Editar Nivel de Agua
        </div>
        <form action="" v-on:submit.prevent="editNivelAgua()">
            <div class="card-body">
                <nivel-agua-form :nivelAgua="nivelAgua" :errors="errors"></nivel-agua-form>
            </div>
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
        data(){
            return {
                nivelAgua: {
                    descripcion: ''
                },
                errors: [],
                loading:false,
                cancel_url: route('nivel-agua.index')
            }
        },
        mounted(){
            let id = this.getIDfromURL();

            axios.get(route('nivel-agua.show', {id:id})).then((response) => {
                this.nivelAgua = response.data.nivelAgua;
            });

        },
        methods: {
            editNivelAgua(){
                this.loading = true;
                axios.put(route('nivel-agua.update', {id:this.nivelAgua.id}), this.nivelAgua).then( (response) =>{
                    if(response.data.success)
                    {
                        window.location.href = this.cancel_url;
                    }

                    this.loading = false;

                }).catch((error) =>{
                    if(error.response.status == 422)
                    {
                        this.errors = error.response.data.errors;
                    }

                    this.loading = false;

                })
            },
            getIDfromURL(){
                let path = window.location.pathname;
                let segments = path.split("/");
                let id = null;

                for(let segment of segments)
                {
                    if(!isNaN(segment))
                    {
                        if(!id)
                        {
                            id = segment;
                        }
                    }
                }

                return id;
            }
        }
    }
</script>