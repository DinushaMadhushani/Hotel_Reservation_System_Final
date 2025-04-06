<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Gallery Page</title>
  <style>
    /* General Reset */
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: Arial, sans-serif;
      background-color: #f4f4f4;
      color: #333;
      line-height: 1.6;
    }

    header {
      text-align: center;
      padding: 20px;
      background-color: #333;
      color: #fff;
    }

    main {
      padding: 20px;
    }

    /* Gallery Grid */
    .gallery {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      gap: 15px;
    }

    .gallery-item {
      position: relative;
      overflow: hidden;
      cursor: pointer;
    }

    .gallery-item img {
      width: 100%;
      height: auto;
      display: block;
      transition: transform 0.3s ease;
    }

    .gallery-item:hover img {
      transform: scale(1.1);
    }

    /* Overlay Effect */
    .overlay {
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: rgba(0, 0, 0, 0.6);
      color: #fff;
      display: flex;
      justify-content: center;
      align-items: center;
      opacity: 0;
      transition: opacity 0.3s ease;
    }

    .gallery-item:hover .overlay {
      opacity: 1;
    }

    /* Lightbox Modal */
    .lightbox {
      display: none;
      position: fixed;
      z-index: 1000;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background-color: rgba(0, 0, 0, 0.9);
      justify-content: center;
      align-items: center;
    }

    .lightbox-content {
      max-width: 90%;
      max-height: 90%;
    }

    .close {
      position: absolute;
      top: 20px;
      right: 30px;
      color: #fff;
      font-size: 40px;
      font-weight: bold;
      cursor: pointer;
    }

    .close:hover {
      color: #ccc;
    }
  </style>
</head>
<body>
  <header>
    <h1>My Gallery</h1>
  </header>

  <main>
    <div class="gallery">
      <!-- Gallery Item 1 -->
      <div class="gallery-item">
        <img src="../assets/images/gallery/img1.jpg " alt="Image 1">
        <div class="overlay">
          <span>View Image</span>
        </div>
      </div>

      <!-- Gallery Item 2 -->
      <div class="gallery-item">
        <img src="https://via.placeholder.com/400x300?text=Image+2" alt="Image 2">
        <div class="overlay">
          <span>View Image</span>
        </div>
      </div>

      <!-- Gallery Item 3 -->
      <div class="gallery-item">
        <img src="https://via.placeholder.com/400x300?text=Image+3" alt="Image 3">
        <div class="overlay">
          <span>View Image</span>
        </div>
      </div>


       <!-- Gallery Item 4 -->
       <div class="gallery-item">
        <img src="https://via.placeholder.com/400x300?text=Image+3" alt="Image 3">
        <div class="overlay">
          <span>View Image</span>
        </div>
      </div>


       <!-- Gallery Item 5 -->
       <div class="gallery-item">
        <img src="https://via.placeholder.com/400x300?text=Image+3" alt="Image 3">
        <div class="overlay">
          <span>View Image</span>
        </div>
      </div>



       <!-- Gallery Item 6 -->
       <div class="gallery-item">
        <img src="https://via.placeholder.com/400x300?text=Image+3" alt="Image 3">
        <div class="overlay">
          <span>View Image</span>
        </div>
      </div>



       <!-- Gallery Item 7 -->
       <div class="gallery-item">
        <img src="https://via.placeholder.com/400x300?text=Image+3" alt="Image 3">
        <div class="overlay">
          <span>View Image</span>
        </div>
      </div>



       <!-- Gallery Item 8 -->
       <div class="gallery-item">
        <img src="https://via.placeholder.com/400x300?text=Image+3" alt="Image 3">
        <div class="overlay">
          <span>View Image</span>
        </div>
      </div>



       <!-- Gallery Item 9 -->
       <div class="gallery-item">
        <img src="https://via.placeholder.com/400x300?text=Image+3" alt="Image 3">
        <div class="overlay">
          <span>View Image</span>
        </div>
      </div>


       <!-- Gallery Item 10 -->
       <div class="gallery-item">
        <img src="https://via.placeholder.com/400x300?text=Image+3" alt="Image 3">
        <div class="overlay">
          <span>View Image</span>
        </div>
      </div>





       <!-- Gallery Item 11 -->
       <div class="gallery-item">
        <img src="https://via.placeholder.com/400x300?text=Image+3" alt="Image 3">
        <div class="overlay">
          <span>View Image</span>
        </div>
      </div>


       <!-- Gallery Item 12 -->
       <div class="gallery-item">
        <img src="https://via.placeholder.com/400x300?text=Image+3" alt="Image 3">
        <div class="overlay">
          <span>View Image</span>
        </div>
      </div>
      <!-- Add more gallery items as needed -->
    </div>
  </main>

  <footer>
    <p style="text-align: center; padding: 10px;">&copy; 2023 My Gallery</p>
  </footer>

  <!-- Lightbox Modal -->
  <div id="lightbox" class="lightbox">
    <span class="close">&times;</span>
    <img class="lightbox-content" id="lightbox-img">
  </div>

  <script>
    document.addEventListener("DOMContentLoaded", () => {
      const galleryItems = document.querySelectorAll(".gallery-item");
      const lightbox = document.getElementById("lightbox");
      const lightboxImg = document.getElementById("lightbox-img");
      const closeBtn = document.querySelector(".close");

      // Open lightbox when an image is clicked
      galleryItems.forEach((item) => {
        item.addEventListener("click", () => {
          const imgSrc = item.querySelector("img").src;
          lightbox.style.display = "flex";
          lightboxImg.src = imgSrc;
        });
      });

      // Close lightbox when the close button is clicked
      closeBtn.addEventListener("click", () => {
        lightbox.style.display = "none";
      });

      // Close lightbox when clicking outside the image
      lightbox.addEventListener("click", (e) => {
        if (e.target === lightbox) {
          lightbox.style.display = "none";
        }
      });
    });
  </script>
</body>
</html>