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
	'entry_south':
		{
		'address': "rtsp://192.168.11.252",
		'function':'entry',
		'slug':'ensouth'
		},
	'entry_north':
		{'address': "rtsp://192.168.11.251", 'function':'entry',
		'slug':'en'},
	'exit':
		{'address': "rtsp://192.168.11.250", 'function':'exit',
		'slug':'exit'},

}

openCameraAddress = cameraAddresses[openCamera]['address']
cameraFunction  = cameraAddresses[openCamera]['function']
cameraSlug  = cameraAddresses[openCamera]['slug']

video = "alpr/samples/rwv-1.mp4"
frameQueue = queue.Queue(maxsize=100)
frame = None
globalFrame = None

cameraThread = threading.Thread(target=camera.open, args=(openCameraAddress,), kwargs={'movement':cameraFunction, 'queue':frameQueue})
cameraThread.start()

detectionThread = threading.Thread(target=plate.recognize, args=(), kwargs={'queue':frameQueue, 'frame':frame, 'camera':cameraSlug})
detectionThread.start()

frameQueue.join()


