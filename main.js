import Map from 'ol/Map.js';
import OSM from 'ol/source/OSM.js';
import TileLayer from 'ol/layer/Tile.js';
import VectorLayer from 'ol/layer/Vector';
import VectorSource from 'ol/source/Vector';
import View from 'ol/View.js';
import Feature from 'ol/Feature';
import * as Style from 'ol/style';
import Point from 'ol/geom/Point';

import {
  fromLonLat
} from 'ol/proj';


const map = new Map({
  layers: [
    new TileLayer({
      source: new OSM(),
    }),
  ],
  target: 'map',
  view: new View({
    center: fromLonLat([23.5, 55.2]),
    zoom: 8,
  }),
});

var vs = new VectorSource({});
var vl = new VectorLayer({
    source: vs
});

function getStyleCircle(radius) {
  return new Style.Style({
      image: new Style.Circle({
          radius: radius * 3,
          fill: new Style.Fill({
              color: 'yellow'
          }),
          stroke: new Style.Stroke({
              color: [0, 0, 255],
              width: 2
          })
      }),
  })
}

function getStyleLabel(callsign) {
  return new Style.Style({
      text: new Style.Text({
          font: '12px Calibri,sans-serif',
          overflow: true,
          fill: new Style.Fill({
              color: '#000'
          }),
          stroke: new Style.Stroke({
              color: '#fff',
              width: 3
          }),
          text: callsign,
          offsetY: 15
      })
  })
}


function addMapEntries(items) {
  for (var i = 0; i < items.length; i++) {

      if (items[i].loc) {

          var f = new Feature({
              name: items[i].callsign,
              geometry: new Point(fromLonLat(
                latLonForGrid(items[i].loc).reverse()
              ))
          });
          f.setStyle([
              getStyleCircle(2),
              getStyleLabel(items[i].callsign)
          ])
          vs.addFeature(f);
      } else {
          console.log(items[i]);
      }
  }

  map.addLayer(vl);
}


document.addEventListener('DOMContentLoaded', function() {
  var userLocation = document.querySelector('.js-user-location');
  var json = userLocation.dataset.points;
  alert(json);
  var data = JSON.parse(json);

  addMapEntries(data.participants);
});
