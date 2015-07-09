//#include "Permalink.js

L.Control.Permalink.include({
	/*
	options: {
		useMarker: true,
		markerOptions: {}
	},
	*/

	initialize_layer: function() {
		this.on('update', this._set_drawing, this);
		this.on('add', this._onadd_layer, this);
	},

	_onadd_layer: function(e) {
		this._map.on('draw:created draw:edited draw:deleted', this._update_drawing, this);
		this._update_drawing();
	},

	_update_drawing: function() {
		if (!this.options.drawnItems) return;

		var drawnItems = this.options.drawnItems;

		if(drawnItems.getLayers().length > 0)
		{
			if (!JSON.stringify)
				return console.error('A Browser with JSON.stringify support required, to save drawings to Permalink')

			this._update({drawnItems: JSON.stringify(drawnItems.toGeoJSON()) });
		}
	},

	_set_drawing: function(e) {
		var p = e.params;
		if (!this.options.layers) return;
		if (! p.drawnItems) return;

		if (!JSON.parse)
			return console.error('A Browser with JSON.parse support required, to load drawings from Permalink')

		var canvas = this.options.drawnItems;
		var drawnItems = JSON.parse(p.drawnItems);
		var newItems = new L.GeoJSON(drawnItems);
		
		canvas.eachLayer(canvas.removeLayer, canvas);
		newItems.eachLayer(canvas.addLayer, canvas);
	}
});
