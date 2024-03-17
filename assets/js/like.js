

const buttons = document.querySelectorAll(".likeButton")


function like(event)
{
    event.preventDefault()
    console.log(this.href)
    fetch(this.href)
        .then(response => response.json())
        .then((data) =>{
            this.querySelector('.nbrLikes').textContent = data.count
            console.log(data)
            let className = "bi-hand-thumbs-up"
            let toReplace = className
            let replacement = className+"-fill"
            if(!data.isLiked){
                toReplace = replacement
                replacement = className
            }
            this.querySelector('.thumb').classList.replace(toReplace, replacement)
        })
}


buttons.forEach((button)=>{
    button.addEventListener("click", like)
})