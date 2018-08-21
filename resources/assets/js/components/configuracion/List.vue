<template>
    <div class="row">
        <div class="col-sm-12 table-responsive">
            <table class="table table bordered">
                <thead>
                    <tr>
                        <th class="number-col">#</th>
                        <th>
                            Nombre
                        </th>
                        <th>
                            Descripcion
                        </th>
                        <th>
                            Valor
                        </th>
                        <th class="action-col">
                            
                        </th>
                    </tr>
                    <tr v-for="(configuracion, index) in configuracions">
                        <td>
                            {{index + 1}}
                        </td>
                        <td>
                            {{configuracion.nombre}}
                        </td>
                        <td>
                            {{configuracion.descripcion}}
                        </td>
                        <td>
                            {{configuracion.valor}}
                        </td>
                        <td>
                            <a :href="getEdiUrl(configuracion.id)" class="btn btn-primary">
                                Editar
                            </a>
                            <a href="#" class="btn btn-danger" v-on:click="deleteDirectorio(configuracion)">
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
                configuracions: []
            }
        },
        mounted() {
            this.getNivelesAgua();
        },
        methods: {

            deleteDirectorio(configuracion){
                let r = confirm('Estas seguro de eliminar este registro');

                if(r === true)
                {
                    axios.delete(route('configuracion.destroy', {id: configuracion.id})).then((response)=>{
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
                axios.get(route('configuracion.all')).then((response) => {
                    this.configuracions = response.data.configuracions;
                });
            },
            getEdiUrl(id){
                return route('configuracion.edit', {id: id})
            }

        }
    }
</script>