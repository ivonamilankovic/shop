import React, {useState, useEffect} from 'react';
import Loading from "../Components/Loading";
import ProductList from "./ProductList";

import Routing from '../../../vendor/friendsofsymfony/jsrouting-bundle/Resources/public/js/router.min.js';
const routes = require('../routes.json');
Routing.setRoutingData(routes);

const ProductsApp = () =>{

    const [products, setProducts] = useState([]);
    const [isLoading,setIsLoading] = useState(false);

    const fetchProducts =  async ()=>{
        setIsLoading(true);
        try{
            const response = await fetch(Routing.generate('app_products_get_all'));
            const data = await response.json();
            setProducts(JSON.parse(data));
        }catch (e){
            console.log(e);
        }
        setIsLoading(false);
    }

    useEffect(()=>{
        fetchProducts();
    },[]);

    if(isLoading){
        return <Loading/>
    }

    return (
            <ProductList products={products}/>
    );

}

export default ProductsApp;

