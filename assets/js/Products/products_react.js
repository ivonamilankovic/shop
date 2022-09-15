import React from "react";
import ReactDOM from 'react-dom/client';
import ProductsApp from "./ProductsApp";

const root = ReactDOM.createRoot(
    document.getElementById('all_products')
);
root.render(<React.StrictMode><ProductsApp/></React.StrictMode>);