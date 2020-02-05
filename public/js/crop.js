/* import Cropper from 'js/cropper';
import Routing from '../../vendor/friendsofsymfony/jsrouting-bundle/Resources/public/js/router'
import Routes from './routes.json'
import axios from 'axios'

Routing.setRoutingData(Routes) 

let cropper;*/
let preview = document.getElementById('avatar')
var file_input = document.getElementById('users_imageFile')
function previewFile()
{
    let file = file_input.files[0]
    let reader = new FileReader()

    reader.addEventListener('load', function (event)
    {
        preview.src = reader.result
    }, false)

    if (file)
    {
        reader.readAsDataURL(file)
    }
}

document.getElementById('avatar').addEventListener('load', function (event)
{
    cropper = new Cropper(preview, {
        aspectRatio: 1/1
    })
})

let form = document.getElementById('profile_user')
form.addEventListener('submit', function (event)
{
    event.preventDefault()
    cropper.getCroppedCanvas({
        maxHeight: 1000,
        maxWidth: 1000
    }).toBlob(function (blob)
    {
        ajaxWithAxios(blob)
    })
})

function ajaxWithAxios(blob)
{
    //let url = Routing.generate('image')
    let url = '/image'
    let data = new FormData(form)
    data.append('file', blob)
    axios({
        method: 'post',
        url: url,
        data: data,
        headers: {'X-Requested-With': 'XMLHttpRequest'}
    })
    .then((response) => {
        console.log(response)
    })
    .catch((error) => {
        console.error(error)
    })
} 