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
                    <tr v-for="(coordinacion, index) in coordinacions">
                        <td>
                            {{index + 1}}
                        </td>
                        <td>
                            {{coordinacion.descripcion}}
                        </td>
                        <td>
                            <a :href="getEdiUrl(coordinacion.id)" class="btn btn-primary">
                                Editar
                            </a>
                            <a href="#" class="btn btn-danger" v-on:click="deleteDirectorio(coordinacion)">
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
                coordinacions: []
            }
        },
        mounted() {
            this.getNivelesAgua();
        },
        methods: {

            deleteDirectorio(coordinacion){
                let r = confirm('Estas seguro de eliminar este registro');

                if(r === true)
                {
                    axios.delete(route('coordinacion.destroy', {id: coordinacion.id})).then((response)=>{
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
                axios.get(route('coordinacion.all')).then((response) => {
                    this.coordinacions = response.data.coordinacions;
                });
            },
            getEdiUrl(id){
                return route('coordinacion.edit', {id: id})
            }

        }
    }
</script>