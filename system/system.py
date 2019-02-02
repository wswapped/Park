import os
import _thread
import threading
import cv2
import pdb
import cProfile as cp
import camera
import plate
import queue
import time
import argparse


parser = argparse.ArgumentParser()
parser.add_argument("camera", help="Enter camera")
args = parser.parse_args()

openCamera = args.camera

cameraAddresses = {
	'entry_south':"rtsp://192.168.11.252",
	'entry_north':"rtsp://192.168.11.251",
	'exit':"rtsp://192.168.11.250",

}

openCameraAddress = cameraAddresses[openCamera]

entryCameraServer = "rtsp://192.168.11.251"
exitCameraServer = "rtsp://192.168.11.251"

video = "alpr/samples/rwv-1.mp4"
frameQueue = queue.Queue(maxsize=100)
frame = None
globalFrame = None

cameraThread = threading.Thread(target=camera.open, args=(openCameraAddress,), kwargs={'movement':'entry', 'queue':frameQueue})
cameraThread.start()

detectionThread = threading.Thread(target=plate.recognize, args=(), kwargs={'queue':frameQueue, 'frame':frame})
detectionThread.start()

frameQueue.join()


