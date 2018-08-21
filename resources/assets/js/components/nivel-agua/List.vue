<template>
    <div class="row">
        <div class="col-sm-12 table-responsive">
            <table class="table table bordered">
                <thead>
                    <tr>
                        <th class="number-col">#</th>
                        <th>
                            Descripción
                        </th>
                        <th class="action-col">
                            
                        </th>
                    </tr>
                    <tr v-for="(nivelAgua, index) in nivelesAgua">
                        <td>
                            {{index + 1}}
                        </td>
                        <td>
                            {{nivelAgua.descripcion}}
                        </td>
                        <td>
                            <a :href="getEdiUrl(nivelAgua.id)" class="btn btn-primary">
                                Editar
                            </a>
                            <a href="#" class="btn btn-danger" v-on:click="deleteNivelAgua(nivelAgua)">
                                Eliminar
                            </a>
                        </td>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</template>

<script>
    export default {
        data(){
            return {
                nivelesAgua: []
            }
        },
        mounted() {
            this.getNivelesAgua();
        },
        methods: {

            deleteNivelAgua(nivelAgua){
                let r = confirm('Estas seguro de eliminar este registro');

                if(r === true)
                {
                    axios.delete(route('nivel-agua.destroy', {id: nivelAgua.id})).then((response)=>{
                        if(response.data.success){
                            this.getNivelesAgua();
                        }else{
                            alert(response.data.message);
                        }
                    }).catch((error)=>{
                        console.log(error.data);
                    });
                }
            },
            getNivelesAgua(){
                axios.get(route('nivel-agua.all')).then((response) => {
                    this.nivelesAgua = response.data.nivelesAgua;
                });
            },
            getEdiUrl(id){
                return route('nivel-agua.edit', {id: id})
            }

        }
    }
</script>