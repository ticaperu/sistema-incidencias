<template>
    <div class="card-body">
        <div class="form-group">
            <label>Descripcion</label>
            <input type="text" class="form-control" placeholder="Ingrese una descripción" name="descripcion" v-model="territorioVecinal.descripcion">
            <small id="passwordHelpBlock" class="form-text text-danger" v-if="errors.descripcion" v-for="error in errors.descripcion" >
                {{error}}
            </small>
        </div>

        <div class="form-group">
            <input type="text" class="form-control" placeholder="Ingrese una dirección de referencia" name="address" v-model="address" v-on:change="setMap()">
        </div>

        <div class="form-group">
            <div id="map" style="height: 300px; width: 100%">

            </div>
        </div>
    </div>
</template>

<script>
    export default {
        props: ['territorioVecinal', 'errors'],
        data() {
            return {
                address: '',
                map:{},
                polygon: {},
                marker: {},
                points:[]
            }
        },
        mounted(){

            this.map = new GMaps({
                div: '#map',
                zoom: 15,
                lat: this.territorioVecinal.latitude ? this.territorioVecinal.latitude: -12.043333,
                lng: this.territorioVecinal.longitude ? this.territorioVecinal.longitude:-77.028333
            });

           /* this.marker = this.map.addMarker({
                lat: this.territorioVecinal.latitude ? this.territorioVecinal.latitude: -12.043333,
                lng: this.territorioVecinal.longitude ? this.territorioVecinal.longitude:-77.028333,
                title: 'Lima',
                click: function(e) {
                    alert('You clicked in this marker');
                }
            });*/


            if(this.territorioVecinal.coordenadas)
            {
                this.points = this.territorioVecinal.coordenadas;
            }else{
                this.points = [
                    [Number(-12.043333) - 0.001, Number(-77.028333) - 0.001],
                    [Number(-12.043333) + 0.001,Number(-77.028333) - 0.001],
                    [Number(-12.043333) + 0.001,Number(-77.028333) + 0.001],
                ]
            }

            this.polygon = this.map.drawPolygon({
                paths: this.points,
                editable: true,
                strokeColor: '#e92b34',
                strokeOpacity: 1,
                strokeWeight: 3,
                fillColor: '#2242da',
                fillOpacity: 0.6
            });

            google.maps.event.addListener(this.polygon.getPath(), "insert_at", ()=>{
                this.getPolygonCoords();
            });
            google.maps.event.addListener(this.polygon.getPath(), "set_at", ()=>{
                this.getPolygonCoords();
            });

            this.getPolygonCoords();

        },
        methods:{
            setMap(){
                GMaps.geocode({
                    address: this.address,
                    callback: (results, status) => {
                        if (status == 'OK') {
                            let latlng = results[0].geometry.location;
                            this.territorioVecinal.latitude = latlng.lat();
                            this.territorioVecinal.longitude = latlng.lng();
                            this.map.setCenter(latlng.lat(), latlng.lng());
                            //this.marker.setPosition(latlng);
                            this.points = [
                                [Number(latlng.lat()) - 0.001, Number(latlng.lng()) - 0.001],
                                [Number(latlng.lat()) + 0.001,Number(latlng.lng()) - 0.001],
                                [Number(latlng.lat()) + 0.001,Number(latlng.lng()) + 0.001],
                            ];

                            this.polygon.setMap(null);

                            this.polygon = this.map.drawPolygon({
                                paths: this.points,
                                editable: true,
                                strokeColor: '#e92b34',
                                strokeOpacity: 1,
                                strokeWeight: 3,
                                fillColor: '#2242da',
                                fillOpacity: 0.6
                            });

                            google.maps.event.addListener(this.polygon.getPath(), "insert_at", ()=>{
                                this.getPolygonCoords();
                            });
                            google.maps.event.addListener(this.polygon.getPath(), "set_at", ()=>{
                                this.getPolygonCoords();
                            });

                            this.getPolygonCoords();

                        }
                    }
                });
            },
            getPolygonCoords(){
                let len = this.polygon.getPath().getLength();
                let coordinates = "";

                for (var i = 0; i < len; i++) {
                    if(i == (len - 1))
                    {
                        coordinates += this.polygon.getPath().getAt(i).toUrlValue(5);
                    }else{
                        coordinates += this.polygon.getPath().getAt(i).toUrlValue(5) + ";";
                    }
                }

                this.territorioVecinal.coordenadas = coordinates;
            }
        }
    }
</script>