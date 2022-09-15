import React  from "react";
import {ThreeDots} from 'react-loader-spinner';

const Loading = () =>{
     return (
         <div className='container w-25'>
             <ThreeDots
                 height="80"
                 width="80"
                 radius="9"
                 color="#0dcaf0"
                 ariaLabel="three-dots-loading"
                 wrapperStyle={{ display: 'block',
                     marginLeft: 'auto',
                     marginRight: 'auto',
                     width: '50%',
                     paddingTop: '180px'
                 }}
                 wrapperClassName=""
                 visible={true}
             />
         </div>
     );
}

export default Loading;