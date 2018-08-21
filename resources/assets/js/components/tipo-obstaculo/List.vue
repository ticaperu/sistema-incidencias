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
                    <tr v-for="(tipoObstaculo, index) in tiposObstaculos">
                        <td>
                            {{index + 1}}
                        </td>
                        <td>
                            {{tipoObstaculo.descripcion}}
                        </td>
                        <td>
                            <a :href="getEdiUrl(tipoObstaculo.id)" class="btn btn-primary">
                                Editar
                            </a>
                            <a href="#" class="btn btn-danger" v-on:click="deleteTipoObstaculo(tipoObstaculo)">
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
                tiposObstaculos: []
            }
        },
        mounted() {
            this.getTiposObstaculos();
        },
        methods: {

            deleteTipoObstaculo(tipoObstaculo){
                let r = confirm('Estas seguro de eliminar este registro');

                if(r === true)
                {
                    axios.delete(route('tipo-obstaculo.destroy', {id: tipoObstaculo.id})).then((response)=>{
                        if(response.data.success){
                            this.getTiposObstaculos();
                        }else{
                            alert(response.data.message);
                        }
                    }).catch((error)=>{
                        console.log(error.data);
                    });
                }
            },
            getTiposObstaculos(){
                axios.get(route('tipo-obstaculo.all')).then((response) => {
                    this.tiposObstaculos = response.data.tiposObstaculos;
                });
            },
            getEdiUrl(id){
                return route('tipo-obstaculo.edit', {id: id})
            }

        }
    }
</script>