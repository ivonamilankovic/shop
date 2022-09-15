import React from "react";
import $ from 'jquery';
import 'timeago';

const ProductList = ({products}) =>{

    const getShortDescription = (desc) =>{
        return desc.substr(0,300)+'...';
    }

    $('.timeago').timeago();

    return (
        <>
        {products.map((product, index)=>{
            const {name,price,description, createdAt, id} = product;
            return <div className="w-75 mx-auto" key={index}>
                <div className="p-3 my-3 border rounded shadow">
                    <div className="d-flex justify-content-between mt-2">
                        <a className="h3 text-decoration-none text-black"
                           href={`/products/${id}`}>{name}</a>
                        <div className="p-2 rounded d-inline text-warning fst-italic fw-bold">${price}</div>
                    </div>
                    <hr/>
                    <p className="mx-5">{getShortDescription(description)} </p>
                    <hr/>
                    <div className="text-end">
                        <small className="d-block text-muted">Published: <time className="timeago" dateTime={createdAt}></time></small>
                    </div>
                </div>
            </div>
            })}
            </>
    );
}

export default ProductList;