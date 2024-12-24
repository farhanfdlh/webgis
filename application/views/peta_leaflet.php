<!-- <h1>Nama: Farhan Fadhilah Rasyid</h1>
<h1>NIM: 11210930000111</h1> -->

<div class="content">
    <div id="map" style="width: 100%; height: 560px; color:black;"></div>
</div>
<script>
    var prov = new L.LayerGroup();
    var faskes = new L.LayerGroup();
    var sungai = new L.LayerGroup();
    var provin = new L.LayerGroup();
    var restoran = new L.LayerGroup();
    var kec_cinere = new L.LayerGroup();
    var jalan_kec_cinere = new L.LayerGroup();

    var map = L.map('map', {
        center: [-1.7912604466772375, 116.42311966554416],
        zoom: 5,
        zoomControl: false,
        layers: []
    });
    var GoogleSatelliteHybrid = L.tileLayer('https://mt1.google.com/vt/lyrs=y&x={x}&y={y}&z={z}', {
        maxZoom: 22,
        attribution: 'Latihan Web GIS'
    }).addTo(map);

    var Esri_NatGeoWorldMap = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/NatGeo_World_Map/MapServer/tile/{z}/{y}/{x}', {
        attribution: 'Tiles &copy; Esri &mdash; National Geographic, Esri, DeLorme, NAVTEQ, UNEP-WCMC, USGS, NASA, ESA, METI, NRCAN, GEBCO, NOAA, iPC',
        maxZoom: 16
    });

    var GoogleMaps = new L.TileLayer('https://mt1.google.com/vt/lyrs=m&x={x}&y={y}&z={z}', {
        opacity: 1.0,
        attribution: 'Latihan Web GIS'
    });
    var GoogleRoads = new L.TileLayer('https://mt1.google.com/vt/lyrs=h&x={x}&y={y}&z={z}', {
        opacity: 1.0,
        attribution: 'Latihan Web GIS'
    });

    var baseLayers = {
        'Google Satellite Hybrid': GoogleSatelliteHybrid,
        'Esri_NatGeoWorldMap': Esri_NatGeoWorldMap,
        'GoogleMaps': GoogleMaps,
        'GoogleRoads': GoogleRoads
    };

    var groupedOverlays = {
        "Peta Dasar": {
            'Ibu Kota Provinsi': prov,
            'Jaringan Sungai': sungai,
            'Provinsi': provin,
            'Fasilitas Kesehatan': faskes
        },
        "Peta Khusus": {
            'Kec.Cinere': kec_cinere,
            'Jalan di Kec. Cinere': jalan_kec_cinere,
            'Restoran Kec.Cinere': restoran
        }
    };

    L.control.groupedLayers(baseLayers, groupedOverlays).addTo(map);

    var osmUrl = 'https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}';
    var osmAttrib = 'Map data &copy; OpenStreetMap contributors';
    var osm2 = new L.TileLayer(osmUrl, {
        minZoom: 0,
        maxZoom: 13,
        attribution: osmAttrib
    });
    var rect1 = {
        color: "#ff1100",
        weight: 3
    };
    var rect2 = {
        color: "#0000AA",
        weight: 1,
        opacity: 0,
        fillOpacity: 0
    };
    var miniMap = new L.Control.MiniMap(osm2, {
        toggleDisplay: true,
        position: "bottomright",
        aimingRectOptions: rect1,
        shadowRectOptions: rect2
    }).addTo(map);

    L.Control.geocoder({
        position: "topleft",
        collapsed: true
    }).addTo(map);

    /* GPS enabled geolocation control set to follow the user's location */
    var locateControl = L.control.locate({
        position: "topleft",
        drawCircle: true,
        follow: true,
        setView: true,
        keepCurrentZoomLevel: true,
        markerStyle: {
            weight: 1,
            opacity: 0.8,
            fillOpacity: 0.8
        },
        circleStyle: {
            weight: 1,
            clickable: false
        },
        icon: "fa fa-location-arrow",
        metric: false,
        strings: {
            title: "My location",
            popup: "You are within {distance} {unit} from this point",
            outsideMapBoundsMsg: "You seem located outside the boundaries of the map"
        },
        locateOptions: {
            maxZoom: 18,
            watch: true,
            enableHighAccuracy: true,
            maximumAge: 10000,
            timeout: 10000
        }
    }).addTo(map);

    var zoom_bar = new L.Control.ZoomBar({
        position: 'topleft'
    }).addTo(map);

    L.control.coordinates({
        position: "bottomleft",
        decimals: 2,
        decimalSeperator: ",",
        labelTemplateLat: "Latitude: {y}",
        labelTemplateLng: "Longitude: {x}"
    }).addTo(map);

    /* scala */
    L.control.scale({
        metric: true,
        position: "bottomleft"
    }).addTo(map);

    var north = L.control({
        position: "bottomleft"
    });
    north.onAdd = function(map) {
        var div = L.DomUtil.create("div", "info legend");
        div.innerHTML = '<img src="<?= base_url() ?>assets/arah-mata-angin.png"style=width:200px;>';
        return div;
    }
    north.addTo(map);

    $.getJSON("<?= base_url() ?>assets/datarestoran.geojson", function(data) {
        var ratIcon = L.icon({
            iconUrl: '<?= base_url() ?>assets/Marker-6.png',
            iconSize: [, 12]
        });

        L.geoJson(data, {
            pointToLayer: function(feature, latlng) {
                var marker = L.marker(latlng, {
                    icon: ratIcon
                });

                // the image path
                var fotoPath = '<?= base_url() ?>assets/foto-restoran/' + feature.properties.foto;
                // popup 
                var popupContent = `
                <div style="width: 250px; font-family: Arial, sans-serif;">
                    <img src="${fotoPath}" style="width: 100%; height: auto; border-radius: 5px; margin-bottom: 10px;">
                    <h3 style="font-size: 16px; margin: 0; color: #333;">${feature.properties.nama_tempat}</h3>
                    <p style="margin: 5px 0 10px; font-size: 14px; color: #777;">${feature.properties.kategori}</p>
                    <p style="margin: 0; font-size: 13px; color: #555;">
                        <strong>Buka pukul: </strong>${feature.properties.jam_buka} - ${feature.properties.jam_tutup}<br>
                        <strong>Alamat: </strong>${feature.properties.nama_jalan}, ${feature.properties.nama_kelurahan}, ${feature.properties.nama_kecamatan}<br>
                    </p>
                </div>
            `;

                marker.bindPopup(popupContent);
                return marker;
            }
        }).addTo(restoran);
    });

    $.getJSON("<?= base_url() ?>/assets/jalan_kecamatan_cinere.geojson", function(kode) {
        L.geoJson(kode, {
            style: function(feature) {
                var color,
                    kode = feature.properties.kode;
                if (kode < 2) color = "#B6C2D0";
                else if (kode > 0) color = "#B6C2D0";
                else color = "#B6C2D0"; // no data
                return {
                    color: "#999",
                    weight: 3,
                    color: color,
                    fillOpacity: .8
                };
            },
            onEachFeature: function(feature, layer) {
                layer.bindPopup()
            }
        }).addTo(jalan_kec_cinere);
    });

    $.getJSON("<?= base_url() ?>assets/provinsi.geojson", function(data) {
        var ratIcon = L.icon({
            iconUrl: '<?= base_url() ?>assets/Marker-1.png',
            iconSize: [, 12]
        });
        L.geoJson(data, {
            pointToLayer: function(feature, latlng) {
                var marker = L.marker(latlng, {
                    icon: ratIcon
                });
                marker.bindPopup(feature.properties.CITY_NAME);
                return marker;
            }
        }).addTo(prov);
    });

    $.getJSON("<?= base_url() ?>assets/rsu.geojson", function(data) {
        var ratIcon = L.icon({
            iconUrl: '<?= base_url() ?>assets/Marker-3.png',
            iconSize: [, 12]
        });
        L.geoJson(data, {
            pointToLayer: function(feature, latlng) {
                var marker = L.marker(latlng, {
                    icon: ratIcon
                });
                marker.bindPopup(feature.properties.NAMOBJ);
                return marker;
            }
        }).addTo(faskes);
    });
    $.getJSON("<?= base_url() ?>assets/poliklinik.geojson", function(data) {
        var ratIcon = L.icon({
            iconUrl: '<?= base_url() ?>assets/Marker-4.png',
            iconSize: [, 12]
        });
        L.geoJson(data, {
            pointToLayer: function(feature, latlng) {
                var marker = L.marker(latlng, {
                    icon: ratIcon
                });
                marker.bindPopup(feature.properties.NAMOBJ);
                return marker;
            }
        }).addTo(faskes);
    });

    $.getJSON("<?= base_url() ?>assets/puskesmas.geojson", function(data) {
        var ratIcon = L.icon({
            iconUrl: '<?= base_url() ?>assets/Marker-5.png',
            iconSize: [, 12]
        });
        L.geoJson(data, {
            pointToLayer: function(feature, latlng) {
                var marker = L.marker(latlng, {
                    icon: ratIcon
                });
                marker.bindPopup(feature.properties.NAMOBJ);
                return marker;
            }
        }).addTo(faskes);
    });

    $.getJSON("<?= base_url() ?>/assets/kec_cinere.geojson", function(kode) {
        L.geoJson(kode, {
            style: function(feature) {
                var color,
                    kode = feature.properties.kode;
                if (kode < 2) color = "#475C6C";
                else if (kode > 0) color = "#475C6C";
                else color = "#475C6C"; // no data
                return {
                    color: "#999",
                    weight: 5,
                    color: color,
                    fillOpacity: .8
                };
            },
            onEachFeature: function(feature, layer) {
                layer.bindPopup()
            }
        }).addTo(kec_cinere);
    });

    $.getJSON("<?= base_url() ?>/assets/sungai.geojson", function(kode) {
        L.geoJson(kode, {
            style: function(feature) {
                var color,
                    kode = feature.properties.kode;
                if (kode < 2) color = "#91DAEE";
                else if (kode > 0) color = "#91DAEE";
                else color = "#91DAEE"; // no data
                return {
                    color: "#999",
                    weight: 5,
                    color: color,
                    fillOpacity: .8
                };
            },
            onEachFeature: function(feature, layer) {
                layer.bindPopup()
            }
        }).addTo(sungai);
    });

    $.getJSON("<?= base_url() ?>/assets/provinsi_poligon.geojson", function(kode) {
        L.geoJson(kode, {
            style: function(feature) {
                var fillColor,
                    kode = feature.properties.kode;
                if (kode > 34) fillColor = "#00441b";
                else if (kode > 33) fillColor = "#41ab5d";
                else if (kode > 32) fillColor = "#a1d99b";
                else if (kode > 31) fillColor = "#f7fcf5";
                else if (kode > 30) fillColor = "#253494";
                else if (kode > 29) fillColor = "#2c7fb8";
                else if (kode > 28) fillColor = "#7fcdbb";
                else if (kode > 27) fillColor = "#edf8b1";
                else if (kode > 26) fillColor = "#7a0177";
                else if (kode > 25) fillColor = "#c51b8a";
                else if (kode > 24) fillColor = "#f768a1";
                else if (kode > 23) fillColor = "#fa9fb5";
                else if (kode > 22) fillColor = "#fcbba1";
                else if (kode > 21) fillColor = "#006837";
                else if (kode > 20) fillColor = "#fec44f";
                else if (kode > 19) fillColor = "#c2e699";
                else if (kode > 18) fillColor = "#fee0d2";
                else if (kode > 17) fillColor = "#756bb1";
                else if (kode > 16) fillColor = "#8c510a";
                else if (kode > 15) fillColor = "#01665e";
                else if (kode > 14) fillColor = "#e41a1c";
                else if (kode > 13) fillColor = "#636363";
                else if (kode > 12) fillColor = "#762a83";
                else if (kode > 11) fillColor = "#1b7837";
                else if (kode > 10) fillColor = "#d53e4f";
                else if (kode > 9) fillColor = "#67001f";
                else if (kode > 8) fillColor = "#c994c7";
                else if (kode > 7) fillColor = "#fdbb84";
                else if (kode > 6) fillColor = "#dd1c77";
                else if (kode > 5) fillColor = "#3182bd";
                else if (kode > 4) fillColor = "#f03b20";
                else if (kode > 3) fillColor = "#31a354";
                else if (kode > 2) fillColor = "#78c679";
                else if (kode > 1) fillColor = "#c2e699";
                else if (kode > 0) fillColor = "#ffffcc";
                else fillColor = "#f7f7f7"; // no data

                return {
                    color: "#999",
                    weight: 1,
                    fillColor: fillColor,
                    fillOpacity: .6
                };
            },
            onEachFeature: function(feature, layer) {
                layer.bindPopup(feature.properties.PROV)
            }
        }).addTo(provin);
    });
</script>