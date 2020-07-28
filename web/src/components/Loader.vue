<template>
  <div id="container" class="fill-height fill-width">
    <div class="loader" id="loader">
      <span></span>
      <span></span>
      <span></span>
    </div>
  </div>
</template>
<style scoped>
.loader {
  width: 100px;
  height: 100px;
  border-radius: 100%;
  position: relative;
  margin: 0 auto;
}

/* LOADER 1 */

#loader-1:before,
#loader-1:after {
  content: "";
  position: absolute;
  top: -10px;
  left: -10px;
  width: 100%;
  height: 100%;
  border-radius: 100%;
  border: 10px solid transparent;
  border-top-color: #cf2030;
}

#loader span {
  display: inline-block;
  width: 20px;
  height: 20px;
  border-radius: 100%;
  background-color: #cf2030;
  margin: 35px 5px;
}

#loader span:nth-child(1) {
  animation: bounce 1s ease-in-out infinite;
}

#loader span:nth-child(2) {
  animation: bounce 1s ease-in-out 0.33s infinite;
}

#loader span:nth-child(3) {
  animation: bounce 1s ease-in-out 0.66s infinite;
}

@keyframes bounce {
  0%,
  75%,
  100% {
    -webkit-transform: translateY(0);
    -ms-transform: translateY(0);
    -o-transform: translateY(0);
    transform: translateY(0);
  }

  25% {
    -webkit-transform: translateY(-20px);
    -ms-transform: translateY(-20px);
    -o-transform: translateY(-20px);
    transform: translateY(-20px);
  }
}

#container {
    width: 100%;
    height:100%;
    display:flex;
    justify-content: center;
    align-items: center;
}
</style>