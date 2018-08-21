<template>
    <div class="row">
        <div class="col-sm-12 table-responsive">
            <div class="row">
                <div class="col-sm-4">
                    <div class="input-group">
                        <input type="password" class="form-control" name="password" placeholder="Buscar por dni"
                               id="password" v-model="dni">
                        <div class="input-group-append">
                            <a href="#" class="btn btn-info" v-on:click="getUsers()">
                                Buscar
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <br>
            <table class="table table bordered">
                <thead>
                <tr>
                    <th class="number-col">#</th>
                    <th>
                        Nombre
                    </th>
                    <th>
                        Estado
                    </th>
                    <th>
                        Rol
                    </th>
                    <th>
                        Fecha de Registro
                    </th>
                    <th class="action-col">

                    </th>
                </tr>
                <tr v-for="(user, index) in users">
                    <td>
                        {{index + 1}}
                    </td>
                    <td>
                        {{user.name}} {{user.las_name}}
                    </td>
                    <td>
                        {{user.state}}
                    </td>
                    <td>
                          <span v-for="(rol,index) in user.roles">
                                {{rol.descripcion + ((index +1 ) < user.roles.length ? ' ,' : '' )}}
                          </span>
                    </td>
                    <td>
                        {{user.created_at}}
                    </td>
                    <td>
                        <a :href="getEditUrl(user.id)" class="btn btn-primary">
                            Editar
                        </a>
                        <a href="#" class="btn btn-danger" v-on:click="deleteTipoUser(user)">
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
        data() {
            return {
                users: [],
                dni: ''
            }
        },
        mounted() {
            this.getUsers();
        },
        methods: {

            deleteTipoUser(user) {
                let r = confirm('Estas seguro de eliminar este registro');

                if (r === true) {
                    axios.delete(route('user.destroy', {id: user.id})).then((response) => {
                        if (response.data.success) {
                            this.getUsers();
                        } else {
                            alert(response.data.message);
                        }
                    }).catch((error) => {
                        console.log(error.data);
                    });
                }
            },
            getUsers() {
                axios.get(route('user.all') + '?dni=' + this.dni).then((response) => {
                    this.users = response.data.users;
                });
            },
            getEditUrl(id) {
                return route('user.edit', {id: id});
            }
        }
    }
</script>