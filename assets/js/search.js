import $ from 'jquery';
import Routing from '../../vendor/friendsofsymfony/jsrouting-bundle/Resources/public/js/router.min.js';
const routes = require('./routes.json');
Routing.setRoutingData(routes);

$('#searchButton').addEventListener('click', ()=>{
    searchByNameSample();
});

function searchByNameSample(){
    const nameSample = $('#searchInput').val();

    //TODO serching for products
}