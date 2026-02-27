let csrf = document.querySelector('meta[name="csrf-token"]').content;
let url = document.querySelector('meta[name="url-base"]').content;
paypal.Buttons({
    style: {
        color: 'gold',        // gold | blue | silver | black
        label: 'pay',         // pay | checkout | buynow | paypal
        layout: 'horizontal', // vertical | horizontal
        shape: 'rect',        // rect | pill
        height: 45
    },
    createOrder: async (data, actions) => {        
        const response = await fetch(url + '/paypal/pay', {
            method: 'post',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrf
            }
        });
        const serverResponse = await response.json();
        return serverResponse.orderId;
    },
    onApprove: async (data, actions) => {
        const response = await fetch(url + '/paypal/approve', {
            method: 'post',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrf
            },
            body: JSON.stringify({
                orderId: data.orderID
            })
        });
        const serverResponse = await response.json();
        if(serverResponse.status == 'COMPLETED') {
            window.location.href = url + '/paypal/approved';
        } else {
            window.location.href = url + '/paypal/notapproved';
        }
        return serverResponse.status;
    },
    onCancel: async (data) => {
        await fetch(url + '/paypal/cancel', {
            method: 'post',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrf
            },
            body: JSON.stringify({
                orderId: data.orderID
            })
        });
        window.location.href = url + '/paypal/canceled';
    },
    onError: () => {
        window.location.href = url + '/paypal/error';
    }
}).render('#paypal-container');