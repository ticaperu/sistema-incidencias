<template>
    <div class="row">
        <div class="col-sm-12 table-responsive">
            <table class="table table bordered">
                <thead>
                    <tr>
                        <th class="number-col">#</th>
                        <th>
                            Persona
                        </th>
                        <th>
                            Territorio Vecinal
                        </th>
                        <th class="action-col">
                            
                        </th>
                    </tr>
                    <tr v-for="(alcaldeVecinal, index) in alcaldesVecinales">
                        <td>
                            {{index + 1}}
                        </td>
                        <td>
                            {{alcaldeVecinal.persona.nombres}} {{alcaldeVecinal.persona.ape_paterno}} {{alcaldeVecinal.persona.ape_materno}}
                        </td>
                        <td>
                            {{alcaldeVecinal.territorio_vecinal.descripcion}}
                        </td>
                        <td>
                            <a :href="getEdiUrl(alcaldeVecinal.id)" class="btn btn-primary">
                                Editar
                            </a>
                            <a href="#" class="btn btn-danger" v-on:click="deleteAlcaldeVecinal(alcaldeVecinal)">
                                Baja
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
                alcaldesVecinales: []
            }
        },
        mounted() {
            this.getNivelesAgua();
        },
        methods: {

            deleteAlcaldeVecinal(alcaldeVecinal){
                let r = confirm('Estas seguro de dar de baja este registro');

                if(r === true)
                {
                    axios.delete(route('alcalde-vecinal.destroy', {id: alcaldeVecinal.id})).then((response)=>{
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
                axios.get(route('alcalde-vecinal.all')).then((response) => {
                    this.alcaldesVecinales = response.data.alcaldesVecinales;
                });
            },
            getEdiUrl(id){
                return route('alcalde-vecinal.edit', {id: id})
            }

        }
    }
</script>