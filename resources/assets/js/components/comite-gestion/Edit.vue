<template>
    <div class="card">
        <div class="card-header">
            Editar Comite de Gestión
        </div>
        <form action="" v-on:submit.prevent="editNivelAgua()">
            <div class="card-body">
                <comite-gestion-form v-if="comiteGestion.id" :comiteGestion="comiteGestion" :errors="errors"></comite-gestion-form>
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
                comiteGestion: {
                    descripcion: ''
                },
                errors: [],
                loading:false,
                cancel_url: route('comite-gestion.index')
            }
        },
        mounted(){
            let id = this.getIDfromURL();

            axios.get(route('comite-gestion.show', {id:id})).then((response) => {
                this.comiteGestion = response.data.comiteGestion;
            });

        },
        methods: {
            editNivelAgua(){
                this.loading = true;
                axios.put(route('comite-gestion.update', {id:this.comiteGestion.id}), this.comiteGestion).then( (response) =>{
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