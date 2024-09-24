
    const canvas = document.querySelector("canvas");
    toolBtns = document.querySelectorAll(".tool");
    sizeSlider = document.querySelector("#size-slider");
    clearCanvas = document.querySelector(".clear-canvas");
    nextCanvas = document.querySelector(".next-canvas");
    ctx = canvas.getContext("2d");
    let prevMouseX, prevMouseY, snapshot;
    let isDrawing = false;
    selectedTool = "brush";
    selectedColor = "#000";
    brushWidth = 5;
    
    window.addEventListener("load", () => {
        canvas.width = canvas.offsetWidth;
        canvas.height = canvas.offsetHeight;
    });

    const drawRect = (e) => {
        
        ctx.strokeRect(e.offsetX, e.offsetY, prevMouseX - e.offsetX, prevMouseY - e.offsetY);
    }
    
    const drawCircle = (e) => {
        ctx.beginPath();
        let radius = Math.sqrt(Math.pow((prevMouseX - e.offsetX), 2) + Math.pow((prevMouseY - e.offsetY), 2));
        ctx.arc( prevMouseX, prevMouseY, radius, 0, 2 * Math.PI);
        ctx.stroke();
    }

    const drawTriangle = (e) => {
        ctx.beginPath();
        ctx.moveTo(prevMouseX, prevMouseY);
        ctx.lineTo(e.offsetX, e.offsetY);
        ctx.lineTo(prevMouseX * 2 - e.offsetX, e.offsetY);
        ctx.closePath();
        ctx.stroke();
    }

    const startDraw = (e) => {
        isDrawing = true;
        prevMouseX = e.offsetX;
        prevMouseY = e.offsetY;
        ctx.lineCap = "round";
        ctx.beginPath();
        ctx.lineWidth = brushWidth;
        snapshot = ctx.getImageData(0, 0, canvas.width, canvas.height);
        drawing(e);
    }
    
    const drawing = (e) => {
        if(!isDrawing) return;
        ctx.putImageData(snapshot,0,0);
        if(selectedTool === "brush" || selectedTool === "eraser"){
            ctx.strokeStyle = selectedTool === "eraser" ? "#fff" : selectedColor
            ctx.lineTo(e.offsetX, e.offsetY);
            ctx.stroke();
        }else if(selectedTool === "rectangle"){
            drawRect(e);
        }else if(selectedTool === "circle"){
            drawCircle(e);
        }else if(selectedTool === "triangle"){
            drawTriangle(e);
        }
    }
    
    toolBtns.forEach(btn => {
        btn.addEventListener("click", () => {
            document.querySelector(".options .active").classList.remove("active");
            btn.classList.add("active");
            selectedTool = btn.id;
            console.log(btn.id);
        });
    });

    clearCanvas.addEventListener("click", () => {
        ctx.clearRect(0, 0, canvas.width, canvas.height);
    })

    sizeSlider.addEventListener("change", () => brushWidth = sizeSlider.value);

    canvas.addEventListener("mousedown", startDraw);
    canvas.addEventListener("mouseup", () => isDrawing = false);
    canvas.addEventListener("mousemove", drawing);




