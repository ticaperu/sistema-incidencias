<template>
    <div class="card">
        <div class="card-header">
            Editar Territorio Vecinal
        </div>
        <form action="" v-on:submit.prevent="editTipoPersona()">
            <div class="card-body">
                <territorio-vecinal-form v-if="territorioVecinal.id" :territorioVecinal="territorioVecinal" :errors="errors"></territorio-vecinal-form>
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
                territorioVecinal: {
                },
                errors: [],
                loading:false,
                cancel_url:route('territorio-vecinal.index')
            }
        },
        mounted(){
            let id = this.getIDfromURL();

            axios.get(route('territorio-vecinal.show', {id:id})).then((response) => {
                this.territorioVecinal = response.data.territorioVecinal;
            });

        },
        methods: {
            editTipoPersona(){
                this.loading = true;
                axios.put(route('territorio-vecinal.update', {id:this.territorioVecinal.id}), this.territorioVecinal).then( (response) =>{
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