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
      font-family: 'Poppins', sans-serif;
      background-color: #f9f9f9;
      color: #333;
      line-height: 1.6;
    }

    /* Header Section */
    header {
      text-align: center;
      padding: 40px 20px;
      background: linear-gradient(135deg, #ff7e5f, #feb47b);
      color: white;
    }

    header h1 {
      font-size: 2.5rem;
      font-weight: bold;
      letter-spacing: 2px;
    }

    /* Main Section */
    main {
      padding: 40px 20px;
    }

    /* Gallery Grid */
    .gallery {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      gap: 20px;
    }

    .gallery-item {
      position: relative;
      overflow: hidden;
      border-radius: 12px;
      box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
      transition: transform 0.3s ease;
      cursor: pointer;
    }

    .gallery-item:hover {
      transform: scale(1.05);
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
      bottom: 0;
      left: 0;
      width: 100%;
      padding: 10px;
      background: rgba(0, 0, 0, 0.6);
      color: #fff;
      text-align: center;
      opacity: 0;
      transition: opacity 0.3s ease;
    }

    .gallery-item:hover .overlay {
      opacity: 1;
    }

    .overlay span {
      font-size: 18px;
      font-weight: bold;
      text-transform: uppercase;
    }

    /* Footer */
    footer {
      text-align: center;
      padding: 20px;
      background: linear-gradient(135deg, #ff7e5f, #feb47b);
      color: white;
      margin-top: 40px;
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
      background: rgba(0, 0, 0, 0.9);
      justify-content: center;
      align-items: center;
    }

    .lightbox img {
      max-width: 90%;
      max-height: 90%;
      border-radius: 12px;
      box-shadow: 0 8px 16px rgba(255, 255, 255, 0.1);
    }

    .close {
      position: absolute;
      top: 20px;
      right: 30px;
      color: #fff;
      font-size: 40px;
      font-weight: bold;
      cursor: pointer;
      transition: color 0.3s ease;
    }

    .close:hover {
      color: #ff7e5f;
    }
  </style>
</head>
<body>
  <!-- Header -->
  <header>
    <h1>Our Gallery</h1>
  </header>

  <!-- Main Content -->
  <main>
    <div class="gallery">
      <!-- Gallery Items -->
      <div class="gallery-item" data-src="../assets/images/gallery/img1.jpg">
        <img src="../assets/images/gallery/img1.jpg" alt="Image 1">
        <div class="overlay">
          <span>Image Title 1</span>
        </div>
      </div>
      <div class="gallery-item" data-src="../assets/images/gallery/img2.jpg">
        <img src="../assets/images/gallery/img2.jpg" alt="Image 2">
        <div class="overlay">
          <span>Image Title 2</span>
        </div>
      </div>
      <div class="gallery-item" data-src="../assets/images/gallery/img3.jpg">
        <img src="../assets/images/gallery/img3.jpg" alt="Image 3">
        <div class="overlay">
          <span>Image Title 3</span>
        </div>
      </div>
      <div class="gallery-item" data-src="../assets/images/gallery/img4.jpg">
        <img src="../assets/images/gallery/img4.jpg" alt="Image 4">
        <div class="overlay">
          <span>Image Title 4</span>
        </div>
      </div>
      <div class="gallery-item" data-src="../assets/images/gallery/img5.jpg">
        <img src="../assets/images/gallery/img5.jpg" alt="Image 5">
        <div class="overlay">
          <span>Image Title 5</span>
        </div>
      </div>
      <div class="gallery-item" data-src="../assets/images/gallery/img6.jpg">
        <img src="../assets/images/gallery/img6.jpg" alt="Image 6">
        <div class="overlay">
          <span>Image Title 6</span>
        </div>
      </div>
      <!-- Add more gallery items as needed -->
    </div>
  </main>

  <!-- Footer -->
  <footer>
    <p>&copy; 2023 Your Website Name. All rights reserved.</p>
  </footer>

  <!-- Lightbox Modal -->
  <div id="lightbox" class="lightbox">
    <span class="close">&times;</span>
    <img class="lightbox-img" src="" alt="Lightbox Image">
  </div>

  <script>
    // Lightbox functionality
    document.addEventListener("DOMContentLoaded", () => {
      const galleryItems = document.querySelectorAll(".gallery-item");
      const lightbox = document.getElementById("lightbox");
      const lightboxImg = document.querySelector(".lightbox-img");
      const closeBtn = document.querySelector(".close");

      // Open lightbox when an image is clicked
      galleryItems.forEach((item) => {
        item.addEventListener("click", () => {
          const imgSrc = item.getAttribute("data-src"); // Get the image source
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